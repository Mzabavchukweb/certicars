<?php

namespace App\PdfBrochure;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Deterministic image pipeline for the CertiCheck brochure.
 *
 * Every photo that ends up in the rendered PDF passes through embed():
 *   1. Resolve a storage-key / absolute URL to bytes (R2 public URL, S3 SDK
 *      fallback, or local disk read).
 *   2. Reject HTML 403 payloads dressed as 200, sub-64-byte garbage,
 *      truncated downloads, anything whose magic bytes don't match a
 *      supported format.
 *   3. Decode via GD, downscale to a context-appropriate size, re-encode
 *      JPEG.
 *   4. Return an EmbeddedImage carrying a base64 `data:` URI.
 *
 * Anything that fails any check returns null and is recorded in the manifest
 * with the failure stage. The view branches on null and the section
 * containing it hides itself; the PDF never carries an <img> pointing at a
 * 404, never reserves an empty 110mm hero block, never emits a "42 zdjęć"
 * header above blank pages.
 *
 * Why this isn't the previous BrochureImageEmbedder: that one logged failures
 * to laravel.log only. This one keeps a structured manifest so the admin
 * diagnostic endpoint can serve a per-image breakdown back to whoever is
 * debugging a customer-reported broken PDF.
 */
final class ImageEmbedder
{
    /** Magic-byte heads of the input formats we can ingest. */
    private const MAGIC = [
        'jpeg' => "\xFF\xD8\xFF",
        'png'  => "\x89PNG\r\n\x1A\n",
        'gif'  => 'GIF8',
        'bmp'  => 'BM',
    ];

    private const WEBP_MAGIC = 'RIFF';

    /**
     * Max width (CSS px) + JPEG quality per render-time use. Hero gets more
     * fidelity because it renders at ~110 mm on the cover. Galleries /
     * damage tiles are ~50 mm wide so 800 px is more than enough.
     */
    private const SIZES = [
        'hero'             => ['w' => 1400, 'q' => 85],
        'gallery'          => ['w' => 800,  'q' => 80],
        'damage_main'      => ['w' => 800,  'q' => 80],
        'damage_photo'     => ['w' => 800,  'q' => 80],
        'damage_aggregate' => ['w' => 800,  'q' => 80],
        'all_images'       => ['w' => 1400, 'q' => 85],
    ];

    /** Cache keyed by source path so the same photo isn't fetched twice. */
    private array $cache = [];

    /**
     * Per-image manifest: every attempt logged with outcome. Powers the
     * admin diagnostic endpoint.
     *
     * @var array<int,array{path:string,context:string,outcome:string,stage?:string,
     *                       width?:int,height?:int,bytes?:int,reason?:string}>
     */
    private array $manifest = [];

    /** Aggregate counters surfaced in the end-of-render log. */
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
     * Resolve, validate, normalize, embed. Returns the EmbeddedImage on
     * success or null on any failure (with the reason recorded in the
     * manifest).
     */
    public function embed(?string $path, string $context = 'gallery'): ?EmbeddedImage
    {
        $path = trim((string) $path);
        $this->stats['candidates']++;

        if ($path === '') {
            return $this->skip($path, $context, 'empty_path');
        }

        if (array_key_exists($path, $this->cache)) {
            $this->stats['cached']++;
            // A null cache entry means we already tried and failed.
            if ($this->cache[$path] === null) {
                $this->manifest[] = [
                    'path'    => $path,
                    'context' => $context,
                    'outcome' => 'skipped_cached',
                ];
                return null;
            }
            $this->manifest[] = [
                'path'    => $path,
                'context' => $context,
                'outcome' => 'embedded_cached',
                'bytes'   => $this->cache[$path]->bytes,
            ];
            return $this->cache[$path];
        }

        $bytes = $this->fetchBytes($path, $context);
        if ($bytes === null) {
            return $this->cache[$path] = $this->skip($path, $context, 'fetch_failed');
        }

        $normalized = $this->normalize($bytes, $path, $context);
        if ($normalized === null) {
            return $this->cache[$path] = $this->skip($path, $context, 'normalize_failed');
        }

        [$jpeg, $w, $h] = $normalized;

        $img = new EmbeddedImage(
            dataUri:    'data:image/jpeg;base64,' . base64_encode($jpeg),
            sourcePath: $path,
            context:    $context,
            width:      $w,
            height:     $h,
            bytes:      strlen($jpeg),
        );

        $this->stats['embedded']++;
        $this->manifest[] = [
            'path'    => $path,
            'context' => $context,
            'outcome' => 'embedded',
            'width'   => $w,
            'height'  => $h,
            'bytes'   => $img->bytes,
        ];

        return $this->cache[$path] = $img;
    }

    /** Aggregate counters for the end-of-render log. */
    public function stats(): array
    {
        return $this->stats;
    }

    /** Structured manifest for the admin diagnostic endpoint. */
    public function manifest(): array
    {
        return $this->manifest;
    }

    /** ── Fetching ─────────────────────────────────────────────────── */

    private function fetchBytes(string $path, string $context): ?string
    {
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $this->fetchHttp($path, $context, ['stage' => 'absolute_url']);
        }
        if ($this->isS3 && $this->publicBaseUrl) {
            $url   = rtrim($this->publicBaseUrl, '/') . '/' . ltrim($path, '/');
            $bytes = $this->fetchHttp($url, $context, ['stage' => 'r2_public', 'url' => $url]);
            if ($bytes !== null) return $bytes;
            return $this->fetchViaSdk($path, $context);
        }
        if ($this->isS3) {
            return $this->fetchViaSdk($path, $context);
        }
        return $this->fetchFromLocal($path, $context);
    }

    private function fetchHttp(string $url, string $context, array $base): ?string
    {
        if (!function_exists('curl_init')) {
            $this->logFail($context, $url, $base + ['stage' => 'no_curl']);
            return null;
        }
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_TIMEOUT         => 15,
            CURLOPT_CONNECTTIMEOUT  => 6,
            CURLOPT_FOLLOWLOCATION  => true,
            CURLOPT_MAXREDIRS       => 3,
            CURLOPT_USERAGENT       => 'CertiCarsBrochure/1.0',
            CURLOPT_ACCEPT_ENCODING => '',
        ]);
        $body   = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $type   = (string) curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        $errno  = curl_errno($ch);
        $error  = curl_error($ch);
        curl_close($ch);

        if ($body === false || !is_string($body) || $body === '' || $status !== 200) {
            $this->logFail($context, $url, $base + [
                'stage'        => 'http_status',
                'http_status'  => $status,
                'content_type' => $type,
                'curl_errno'   => $errno,
                'curl_error'   => $error,
                'body_length'  => is_string($body) ? strlen($body) : 0,
            ]);
            return null;
        }
        // Reject content_type that's clearly not an image. Magic-byte check
        // later catches the rest, but content-type is cheap to check and
        // saves us decoding HTML as bytes.
        if ($type !== '' && !str_starts_with(strtolower($type), 'image/')) {
            $this->logFail($context, $url, $base + [
                'stage'        => 'content_type_not_image',
                'content_type' => $type,
            ]);
            return null;
        }
        return $body;
    }

    private function fetchViaSdk(string $path, string $context): ?string
    {
        try {
            $disk = Storage::disk('public');
            if (!$disk->exists($path)) {
                $this->logFail($context, $path, ['stage' => 's3_sdk_missing']);
                return null;
            }
            $bytes = (string) $disk->get($path);
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

    private function fetchFromLocal(string $path, string $context): ?string
    {
        try {
            $disk = Storage::disk('public');
            if (!$disk->exists($path)) {
                $this->logFail($context, $path, ['stage' => 'local_missing']);
                return null;
            }
            $abs   = $disk->path($path);
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

    /** ── Validate + decode + resize + JPEG-encode ─────────────────── */

    /**
     * @return array{0:string,1:int,2:int}|null  [jpeg, width, height]
     */
    private function normalize(string $bytes, string $path, string $context): ?array
    {
        if (strlen($bytes) < 64) {
            $this->logFail($context, $path, ['stage' => 'bytes_too_small', 'len' => strlen($bytes)]);
            return null;
        }
        $ltrim = ltrim($bytes);
        if ($ltrim !== '' && ($ltrim[0] === '<' || $ltrim[0] === '{')) {
            $this->logFail($context, $path, ['stage' => 'looks_like_html_or_json']);
            return null;
        }

        $head   = substr($bytes, 0, 16);
        $format = $this->detectFormat($head);
        if ($format === null) {
            $this->logFail($context, $path, [
                'stage'    => 'unknown_format',
                'magic_hex' => bin2hex($head),
            ]);
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
            $w = imagesx($im);
            $h = imagesy($im);
            if ($w <= 0 || $h <= 0) {
                $this->logFail($context, $path, ['stage' => 'gd_zero_dimensions']);
                return null;
            }
            $cfg  = self::SIZES[$context] ?? self::SIZES['gallery'];
            $maxW = $cfg['w'];
            $q    = $cfg['q'];

            if ($w > $maxW) {
                $newW = $maxW;
                $newH = (int) round($h * ($maxW / $w));
                $dst  = imagecreatetruecolor($newW, $newH);
                imagefilledrectangle($dst, 0, 0, $newW, $newH, imagecolorallocate($dst, 255, 255, 255));
                if (!imagecopyresampled($dst, $im, 0, 0, 0, 0, $newW, $newH, $w, $h)) {
                    imagedestroy($dst);
                    $this->logFail($context, $path, ['stage' => 'gd_resize_failed']);
                    return null;
                }
                imagedestroy($im);
                $im = $dst;
                $w  = $newW;
                $h  = $newH;
            }

            ob_start();
            $ok   = imagejpeg($im, null, $q);
            $jpeg = ob_get_clean();
            if (!$ok || $jpeg === false || $jpeg === '') {
                $this->logFail($context, $path, ['stage' => 'jpeg_encode_failed']);
                return null;
            }

            return [$jpeg, $w, $h];
        } finally {
            if ($im instanceof \GdImage) @imagedestroy($im);
        }
    }

    private function detectFormat(string $head): ?string
    {
        foreach (self::MAGIC as $name => $sig) {
            if (str_starts_with($head, $sig)) return $name;
        }
        if (str_starts_with($head, self::WEBP_MAGIC) && substr($head, 8, 4) === 'WEBP') {
            return 'webp';
        }
        return null;
    }

    /** ── Logging + bookkeeping ────────────────────────────────────── */

    private function skip(string $path, string $context, string $reason): null
    {
        $this->stats['skipped']++;
        $this->manifest[] = [
            'path'    => $path,
            'context' => $context,
            'outcome' => 'skipped',
            'reason'  => $reason,
        ];
        return null;
    }

    private function logFail(string $context, string $pathOrUrl, array $extra): void
    {
        Log::warning('pdf_brochure.image_fail', [
            'report_id' => $this->reportId,
            'ctx'       => $context,
            'path'      => $pathOrUrl,
        ] + $extra);
    }
}
