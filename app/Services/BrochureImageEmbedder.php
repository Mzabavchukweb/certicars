<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Server-side image embedding for the browser-rendered CertiCheck brochure.
 *
 * Why this exists:
 * The previous BrochureImageValidator did a HEAD probe and then handed the
 * URL to Chromium to fetch at render time. In production this consistently
 * produced "PDF says X photos but pages are blank" because HEAD can succeed
 * while the actual Chromium GET fails — different User-Agent, signed-URL
 * timing, R2 bucket CORS, or just a fetch race against Browsershot's
 * networkidle timer. Empty <img> elements then rendered as invisible boxes,
 * the brochure said "42 zdjęć" and shipped 7 blank pages.
 *
 * Pipeline (resolveToDataUri):
 *   1. Empty path → skip cleanly.
 *   2. Fetch bytes via cURL — full GET, not HEAD. Server-side.
 *   3. Validate bytes: non-empty, magic-byte format match, getimagesize() > 0,
 *      reject HTML/JSON 403 payloads.
 *   4. Convert WebP → JPEG when GD has webp support.
 *   5. Resize to context-appropriate dimensions via GD so the inlined HTML
 *      doesn't balloon to 50 MB (a 7-MP DSLR photo embedded raw would
 *      cripple Chromium's parser).
 *   6. Return a data:image/jpeg;base64,... URI. Chromium renders this from
 *      the HTML inline — zero network at render time.
 *
 * Anything that fails any check is returned as null and the template skips
 * it cleanly. The caller is responsible for hiding sections whose image
 * lists end up empty.
 */
class BrochureImageEmbedder
{
    /** Per-context target dimensions (max side length, in CSS pixels). */
    private const CONTEXT_SIZES = [
        'hero'             => ['w' => 1400, 'q' => 85],
        'gallery'          => ['w' => 800,  'q' => 80],
        'damage_main'      => ['w' => 800,  'q' => 80],
        'damage_photo'     => ['w' => 800,  'q' => 80],
        'damage_aggregate' => ['w' => 800,  'q' => 80],
        'all_images'       => ['w' => 1400, 'q' => 85],
    ];

    /** Magic-byte signatures we accept on the wire. */
    private const SUPPORTED_MAGIC = [
        'jpeg' => "\xFF\xD8\xFF",
        'png'  => "\x89PNG\r\n\x1A\n",
        'gif'  => 'GIF8',
        'bmp'  => 'BM',
    ];

    private const WEBP_MAGIC = 'RIFF';

    /** path → data URI cache so duplicated photos aren't re-fetched. */
    private array $cache = [];

    private array $stats = [
        'candidates' => 0,
        'embedded'   => 0,
        'skipped'    => 0,
        'cached'     => 0,
    ];

    public function __construct(
        private readonly string $reportId,
        private readonly ?string $publicBaseUrl,
        private readonly bool $isS3,
    ) {}

    /**
     * Resolve a storage path / absolute URL to a base64 `data:` URI suitable
     * for inline embedding, or null if the image can't be embedded. Callers
     * MUST treat null as "skip this image" and never render an empty <img>.
     */
    public function resolveToDataUri(?string $path, string $context = 'gallery'): ?string
    {
        $path = trim((string) $path);
        $this->stats['candidates']++;

        if ($path === '') {
            $this->stats['skipped']++;
            return null;
        }

        if (array_key_exists($path, $this->cache)) {
            $this->stats['cached']++;
            return $this->cache[$path];
        }

        $bytes = $this->loadBytes($path, $context);
        if ($bytes === null) {
            return $this->cache[$path] = $this->skip($path, $context, 'load_failed');
        }

        $jpeg = $this->normalizeToJpeg($bytes, $path, $context);
        if ($jpeg === null) {
            return $this->cache[$path] = $this->skip($path, $context, 'normalize_failed');
        }

        $this->stats['embedded']++;
        return $this->cache[$path] = 'data:image/jpeg;base64,' . base64_encode($jpeg);
    }

    public function stats(): array
    {
        return $this->stats;
    }

    /**
     * Load image bytes from whichever storage backs this car. S3/R2 goes via
     * the public URL (HTTPS); local goes straight off disk. We never hand
     * the URL to the renderer to fetch — the bytes have to be in PHP's hands
     * before we even build the HTML.
     */
    private function loadBytes(string $path, string $context): ?string
    {
        // Absolute URL — pasted by admin, fetch directly.
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $this->fetchHttp($path, $context, ['stage' => 'absolute_url']);
        }

        // R2/S3 public URL.
        if ($this->isS3 && $this->publicBaseUrl) {
            $url = rtrim($this->publicBaseUrl, '/') . '/' . ltrim($path, '/');
            $bytes = $this->fetchHttp($url, $context, ['stage' => 'r2_public', 'url' => $url]);
            if ($bytes !== null) {
                return $bytes;
            }
            // Last resort: try the SDK path (private bucket fallback).
            return $this->loadViaSdk($path, $context);
        }

        if ($this->isS3) {
            return $this->loadViaSdk($path, $context);
        }

        // Local disk.
        return $this->loadFromLocal($path, $context);
    }

    private function loadViaSdk(string $path, string $context): ?string
    {
        try {
            if (!Storage::disk('public')->exists($path)) {
                $this->logFail($context, $path, ['stage' => 's3_sdk_missing']);
                return null;
            }
            $bytes = (string) Storage::disk('public')->get($path);
            if ($bytes === '') {
                $this->logFail($context, $path, ['stage' => 's3_sdk_empty']);
                return null;
            }
            return $bytes;
        } catch (\Throwable $e) {
            $this->logFail($context, $path, [
                'stage'     => 's3_sdk_exception',
                'exception' => get_class($e),
                'msg'       => $e->getMessage(),
            ]);
            return null;
        }
    }

    private function loadFromLocal(string $path, string $context): ?string
    {
        try {
            $disk = Storage::disk('public');
            if (!$disk->exists($path)) {
                $this->logFail($context, $path, ['stage' => 'local_missing']);
                return null;
            }
            $abs = $disk->path($path);
            $bytes = @file_get_contents($abs);
            if ($bytes === false || $bytes === '') {
                $this->logFail($context, $path, ['stage' => 'local_empty', 'abs' => $abs]);
                return null;
            }
            return $bytes;
        } catch (\Throwable $e) {
            $this->logFail($context, $path, [
                'stage'     => 'local_exception',
                'exception' => get_class($e),
                'msg'       => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Validate bytes → decode → resize → encode JPEG. Returns final JPEG
     * bytes or null if any step rejects the input. We re-encode every
     * image so the output is uniformly JPEG (smallest data: URI overhead
     * and best Chromium compatibility), even if the source was PNG.
     */
    private function normalizeToJpeg(string $bytes, string $path, string $context): ?string
    {
        if (strlen($bytes) < 64) {
            $this->logFail($context, $path, ['stage' => 'bytes_too_small', 'len' => strlen($bytes)]);
            return null;
        }
        // Reject obvious HTML / JSON 403 payloads dressed up as 200 OK.
        $ltrim = ltrim($bytes);
        if ($ltrim !== '' && ($ltrim[0] === '<' || $ltrim[0] === '{')) {
            $this->logFail($context, $path, ['stage' => 'looks_like_html_or_json']);
            return null;
        }

        $head   = substr($bytes, 0, 16);
        $format = $this->detectFormat($head);
        if ($format === null) {
            $this->logFail($context, $path, ['stage' => 'unknown_format', 'magic_head_hex' => bin2hex($head)]);
            return null;
        }
        if ($format === 'webp' && !function_exists('imagecreatefromwebp')) {
            $this->logFail($context, $path, ['stage' => 'webp_no_gd_support']);
            return null;
        }

        $im = @imagecreatefromstring($bytes);
        if ($im === false) {
            $this->logFail($context, $path, ['stage' => 'gd_decode_failed', 'format' => $format]);
            return null;
        }

        try {
            $size  = self::CONTEXT_SIZES[$context] ?? self::CONTEXT_SIZES['gallery'];
            $maxW  = $size['w'];
            $q     = $size['q'];
            $w     = imagesx($im);
            $h     = imagesy($im);
            if ($w <= 0 || $h <= 0) {
                $this->logFail($context, $path, ['stage' => 'gd_zero_dimensions']);
                return null;
            }
            if ($w > $maxW) {
                $newW = $maxW;
                $newH = (int) round($h * ($maxW / $w));
                $resized = imagecreatetruecolor($newW, $newH);
                // Flatten any alpha onto white so PNGs with transparency don't
                // re-encode to JPEG as black blocks.
                imagefilledrectangle($resized, 0, 0, $newW, $newH, imagecolorallocate($resized, 255, 255, 255));
                if (!imagecopyresampled($resized, $im, 0, 0, 0, 0, $newW, $newH, $w, $h)) {
                    imagedestroy($resized);
                    $this->logFail($context, $path, ['stage' => 'gd_resize_failed']);
                    return null;
                }
                imagedestroy($im);
                $im = $resized;
            }
            ob_start();
            $ok = imagejpeg($im, null, $q);
            $jpeg = ob_get_clean();
            if (!$ok || $jpeg === false || $jpeg === '') {
                $this->logFail($context, $path, ['stage' => 'jpeg_encode_failed']);
                return null;
            }
            return $jpeg;
        } finally {
            if (is_resource($im) || $im instanceof \GdImage) {
                @imagedestroy($im);
            }
        }
    }

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

    private function fetchHttp(string $url, string $context, array $logBase): ?string
    {
        if (!function_exists('curl_init')) {
            $this->logFail($context, $url, $logBase + ['stage' => 'no_curl']);
            return null;
        }
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_CONNECTTIMEOUT => 6,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 3,
            CURLOPT_USERAGENT      => 'CertiCarsPDF/1.0',
            CURLOPT_ACCEPT_ENCODING => '',
        ]);
        $body   = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $errno  = curl_errno($ch);
        $error  = curl_error($ch);
        curl_close($ch);

        if ($body === false || !is_string($body) || $body === '' || $status !== 200) {
            $this->logFail($context, $url, $logBase + [
                'stage'        => 'http_failed',
                'http_status'  => $status,
                'curl_errno'   => $errno,
                'curl_error'   => $error,
                'body_length'  => is_string($body) ? strlen($body) : 0,
            ]);
            return null;
        }
        return $body;
    }

    private function skip(string $path, string $context, string $reason): ?string
    {
        $this->stats['skipped']++;
        return null;
    }

    private function logFail(string $context, string $pathOrUrl, array $extra): void
    {
        Log::warning('pdf.image_embed.fail', [
            'report_id' => $this->reportId,
            'ctx'       => $context,
            'path'      => $pathOrUrl,
        ] + $extra);
    }
}
