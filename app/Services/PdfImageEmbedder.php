<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Resolves an image source path (typically what's stored on CarImage::path or
 * CarDamage::image_path) into a LOCAL filesystem path that DomPDF can read
 * with isRemoteEnabled=false.
 *
 * Why this exists:
 * DomPDF cannot reliably fetch R2/S3 URLs at render time, and the prior
 * inline cURL pipeline in PdfController had no byte validation — broken
 * gateway responses, HTML 403 pages, or unsupported WebP frames all wrote a
 * "valid" temp file that DomPDF would then render as a broken-image X.
 *
 * Pipeline (resolveToLocalFile):
 *   1. Empty / placeholder paths → skip cleanly (no broken image).
 *   2. If `$path` is itself an absolute URL → cURL it directly.
 *   3. If FILESYSTEM_DISK=s3 and AWS_URL is set → cURL the public R2 URL.
 *      Falls back to the S3 SDK on HTTP failure (private bucket scenario).
 *   4. Otherwise (local disk) → return the on-disk absolute path.
 *   5. Validate the fetched bytes:
 *      - non-empty
 *      - magic bytes match a supported format
 *      - getimagesize() returns a positive dimension
 *      Anything that fails any check is rejected as `null` (template skips it).
 *   6. WebP / unsupported formats are converted to JPEG when GD is available.
 *   7. Bytes are written to a temp file whose extension matches the format
 *      (DomPDF picks renderer by extension, not magic).
 *
 * Caller is responsible for calling cleanup() after the PDF is rendered.
 */
class PdfImageEmbedder
{
    /** Magic-byte signatures of the formats DomPDF can handle natively. */
    private const SUPPORTED_MAGIC = [
        'jpeg' => "\xFF\xD8\xFF",
        'png'  => "\x89PNG\r\n\x1A\n",
        'gif'  => 'GIF8',
        'bmp'  => 'BM',
    ];

    /** Magic-byte signature for WebP (we convert these). */
    private const WEBP_MAGIC = 'RIFF';

    /** Cache keyed by source path so the same physical file isn't fetched twice. */
    private array $cache = [];

    /** Temp files we created; the caller must call cleanup() after PDF render. */
    private array $tmpFiles = [];

    /** Per-run statistics for the final summary log. */
    private array $stats = ['candidates' => 0, 'success' => 0, 'failed' => 0, 'cached' => 0];

    public function __construct(
        private readonly string $reportId,
        private readonly ?string $publicBaseUrl = null,
        private readonly bool $isS3 = false,
    ) {}

    /**
     * Resolve a source path into a local file path readable by DomPDF, or
     * null if the image cannot be embedded (caller MUST handle null by
     * skipping that image in the template, never emitting an empty <img>).
     *
     * @param string $path        Source path (relative storage key or absolute URL)
     * @param string $context     'hero' / 'gallery' / 'damage_main' / 'damage_photo' — used for log correlation only
     */
    public function resolveToLocalFile(?string $path, string $context = 'unknown'): ?string
    {
        $path = trim((string) $path);
        $this->stats['candidates']++;

        $base = [
            'report_id' => $this->reportId,
            'ctx'       => $context,
            'path'      => $path,
        ];

        if ($path === '') {
            $this->stats['failed']++;
            return null;
        }

        if (isset($this->cache[$path])) {
            $this->stats['cached']++;
            return $this->cache[$path];
        }

        // 1) Absolute URL — fetch directly, useful when an admin pasted a full URL.
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            $result = $this->fetchHttp($path);
            if ($result['body'] === null) {
                $this->failure($base + ['stage' => 'absolute_url_fetch'] + $result['diag']);
                return null;
            }
            return $this->cache[$path] = $this->finalize($result['body'], $path, $base + ['source' => 'absolute_url']);
        }

        // 2) S3/R2 with a public URL configured — credential-free HTTP fetch.
        if ($this->isS3 && $this->publicBaseUrl) {
            $url = $this->publicBaseUrl . '/' . ltrim($path, '/');
            $result = $this->fetchHttp($url);
            if ($result['body'] !== null) {
                return $this->cache[$path] = $this->finalize($result['body'], $path, $base + ['source' => 'r2_public', 'url' => $url]);
            }
            // HTTP failed — try SDK as last resort (private bucket / DNS hiccup).
            Log::warning('pdf.image.r2_public_failed', $base + ['url' => $url] + $result['diag']);
        }

        // 3) S3 SDK fallback (or no public URL configured).
        if ($this->isS3) {
            try {
                if (!Storage::disk('public')->exists($path)) {
                    $this->failure($base + ['stage' => 's3_sdk_missing']);
                    return null;
                }
                $body = (string) Storage::disk('public')->get($path);
                if ($body === '') {
                    $this->failure($base + ['stage' => 's3_sdk_empty']);
                    return null;
                }
                return $this->cache[$path] = $this->finalize($body, $path, $base + ['source' => 's3_sdk']);
            } catch (\Throwable $e) {
                $this->failure($base + ['stage' => 's3_sdk_exception', 'exception' => get_class($e), 'msg' => $e->getMessage()]);
                return null;
            }
        }

        // 4) Local disk — pass the on-disk path straight through (no temp copy needed).
        try {
            if (!Storage::disk('public')->exists($path)) {
                $this->failure($base + ['stage' => 'local_missing']);
                return null;
            }
            $localPath = Storage::disk('public')->path($path);
            if (!is_file($localPath) || filesize($localPath) === 0) {
                $this->failure($base + ['stage' => 'local_not_readable', 'path' => $localPath]);
                return null;
            }
            // Verify it's actually an image (rejects PHP/HTML files that snuck in).
            $info = @getimagesize($localPath);
            if ($info === false) {
                $this->failure($base + ['stage' => 'local_not_image', 'path' => $localPath]);
                return null;
            }
            // WebP local files still need conversion for DomPDF.
            if ($info['mime'] === 'image/webp') {
                $bytes = (string) file_get_contents($localPath);
                return $this->cache[$path] = $this->finalize($bytes, $path, $base + ['source' => 'local_webp']);
            }
            $this->stats['success']++;
            return $this->cache[$path] = $localPath;
        } catch (\Throwable $e) {
            $this->failure($base + ['stage' => 'local_exception', 'exception' => get_class($e), 'msg' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Validate fetched bytes, convert WebP→JPEG when possible, write a temp
     * file whose extension matches the format. Returns null on rejection.
     */
    private function finalize(string $bytes, string $sourcePath, array $logBase): ?string
    {
        // Reject obvious garbage: too small to be a real image, or HTML / JSON
        // payloads that some object stores return on 403 with status 200.
        if (strlen($bytes) < 64) {
            $this->failure($logBase + ['stage' => 'bytes_too_small', 'len' => strlen($bytes)]);
            return null;
        }
        $head = substr($bytes, 0, 16);
        if (str_starts_with(ltrim($bytes), '<') || str_starts_with(ltrim($bytes), '{')) {
            $this->failure($logBase + ['stage' => 'looks_like_html_or_json']);
            return null;
        }

        // Detect format from magic bytes (don't trust source path extension).
        $format = $this->detectFormat($head);
        if ($format === null) {
            $this->failure($logBase + ['stage' => 'unknown_format', 'magic_head_hex' => bin2hex($head)]);
            return null;
        }

        // WebP path: convert to JPEG via GD if available, otherwise reject.
        if ($format === 'webp') {
            if (!function_exists('imagecreatefromwebp')) {
                $this->failure($logBase + ['stage' => 'webp_no_gd_support']);
                return null;
            }
            $im = @imagecreatefromstring($bytes);
            if ($im === false) {
                $this->failure($logBase + ['stage' => 'webp_decode_failed']);
                return null;
            }
            $tmp = $this->newTmpPath('jpg');
            $ok = imagejpeg($im, $tmp, 88);
            imagedestroy($im);
            if (!$ok || !is_file($tmp) || filesize($tmp) === 0) {
                $this->failure($logBase + ['stage' => 'webp_jpeg_write_failed']);
                return null;
            }
            $this->tmpFiles[] = $tmp;
            $this->stats['success']++;
            return $tmp;
        }

        // Final dimension check via getimagesize on the bytes-backed temp file —
        // detects truncated downloads that pass magic-byte check but fail decode.
        $ext = $format === 'jpeg' ? 'jpg' : $format;
        $tmp = $this->newTmpPath($ext);
        $written = file_put_contents($tmp, $bytes);
        if ($written === false || $written === 0) {
            $this->failure($logBase + ['stage' => 'tmp_write_failed', 'tmp' => $tmp]);
            return null;
        }
        $this->tmpFiles[] = $tmp;

        $info = @getimagesize($tmp);
        if ($info === false || ($info[0] ?? 0) <= 0 || ($info[1] ?? 0) <= 0) {
            $this->failure($logBase + ['stage' => 'getimagesize_failed', 'tmp' => $tmp, 'size' => filesize($tmp)]);
            return null;
        }

        $this->stats['success']++;
        return $tmp;
    }

    /** Magic-byte sniffer covering DomPDF's natively-supported formats + webp. */
    private function detectFormat(string $head): ?string
    {
        foreach (self::SUPPORTED_MAGIC as $name => $signature) {
            if (str_starts_with($head, $signature)) {
                return $name;
            }
        }
        if (str_starts_with($head, self::WEBP_MAGIC) && substr($head, 8, 4) === 'WEBP') {
            return 'webp';
        }
        return null;
    }

    /** Returns ['body' => string|null, 'diag' => [...]] without ever throwing. */
    private function fetchHttp(string $url): array
    {
        if (!function_exists('curl_init')) {
            return ['body' => null, 'diag' => ['error' => 'no_curl_extension']];
        }
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 3,
            CURLOPT_USERAGENT      => 'CertiCarsPDF/1.0',
        ]);
        $body   = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $type   = (string) curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        $errno  = curl_errno($ch);
        curl_close($ch);

        $ok = ($body !== false && $status === 200 && is_string($body) && strlen($body) > 0);
        return [
            'body' => $ok ? $body : null,
            'diag' => [
                'http_status'  => $status,
                'content_type' => $type,
                'body_length'  => is_string($body) ? strlen($body) : 0,
                'curl_errno'   => $errno,
            ],
        ];
    }

    private function newTmpPath(string $ext): string
    {
        return sys_get_temp_dir() . '/certicars_pdf_' . bin2hex(random_bytes(8)) . '.' . $ext;
    }

    private function failure(array $context): void
    {
        $this->stats['failed']++;
        Log::warning('pdf.image.fail', $context);
    }

    /** Removes every temp file this run created. Safe to call multiple times. */
    public function cleanup(): void
    {
        foreach ($this->tmpFiles as $tmp) {
            @unlink($tmp);
        }
        $this->tmpFiles = [];
    }

    /** Summary stats for the run, for end-of-run logging. */
    public function stats(): array
    {
        return $this->stats;
    }

    /** Number of temp files currently held (testing helper). */
    public function tmpFileCount(): int
    {
        return count($this->tmpFiles);
    }
}
