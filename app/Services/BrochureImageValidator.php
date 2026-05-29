<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * Validates that an image URL is actually reachable before we feed it to the
 * headless browser, so the PDF never renders a broken-image icon or an empty
 * gray placeholder block.
 *
 * Why HEAD-check at all when the browser will fetch the image anyway?
 * - Some R2 keys point at deleted objects (a soft-deleted upload row that
 *   wasn't garbage-collected). Browser hits 404, gets a "missing image" icon.
 * - Some objects exist but return non-image MIME (a sneaky uploaded text file)
 *   — browser shows alt text, not a photo.
 * - Filtering ahead of time means the brochure HTML never even contains the
 *   <img> tag, so an entire damage card / photo grid cell collapses cleanly
 *   instead of leaving a 130px tall empty box.
 *
 * Failure mode: a single unreachable URL is logged and skipped, never throws.
 */
class BrochureImageValidator
{
    private const TIMEOUT_SECONDS = 6;

    private array $cache = [];

    private array $stats = ['candidates' => 0, 'reachable' => 0, 'skipped' => 0];

    public function __construct(
        private readonly string $reportId,
        private readonly ?string $publicBaseUrl,
        private readonly bool $isS3,
    ) {}

    /**
     * Resolve a storage path / absolute URL into a publicly fetchable image
     * URL, or null if the object isn't reachable / isn't an image. Caller
     * MUST treat null as "skip this image" and never render an empty <img>.
     */
    public function resolveToPublicUrl(?string $path, string $context = 'unknown'): ?string
    {
        $path = trim((string) $path);
        $this->stats['candidates']++;

        if ($path === '') {
            $this->stats['skipped']++;
            return null;
        }

        if (array_key_exists($path, $this->cache)) {
            return $this->cache[$path];
        }

        $url = $this->buildUrl($path);
        if ($url === null) {
            return $this->cache[$path] = $this->skip($path, $context, 'no_public_url');
        }

        $diag = $this->probe($url);
        if (!$diag['ok']) {
            Log::warning('pdf.image_url.unreachable', [
                'report_id' => $this->reportId,
                'ctx'       => $context,
                'path'      => $path,
                'url'       => $url,
            ] + $diag);
            return $this->cache[$path] = $this->skip($path, $context, 'unreachable');
        }

        $this->stats['reachable']++;
        return $this->cache[$path] = $url;
    }

    /** Per-run summary for end-of-render logging. */
    public function stats(): array
    {
        return $this->stats;
    }

    /**
     * Build an absolute, browser-reachable URL from whatever the model carries.
     * Handles three shapes:
     *   - Already an absolute http(s) URL → use as-is
     *   - Storage key + AWS_URL configured → join + return
     *   - Local disk → return null (browser can't reach app://storage), caller
     *     should fall back to the DomPDF pipeline for this image.
     */
    private function buildUrl(string $path): ?string
    {
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        if ($this->isS3 && $this->publicBaseUrl) {
            return rtrim($this->publicBaseUrl, '/') . '/' . ltrim($path, '/');
        }
        return null;
    }

    /**
     * cURL HEAD with GET fallback. Some R2-fronted CDNs reject HEAD with 405;
     * a small ranged GET catches those cases cheaply without downloading the
     * whole image. Returns ['ok' => bool, 'http_status' => int, ...].
     */
    private function probe(string $url): array
    {
        if (!function_exists('curl_init')) {
            return ['ok' => false, 'reason' => 'no_curl'];
        }
        // Try HEAD first.
        [$status, $type, $errno] = $this->probeRequest($url, head: true);
        if ($status === 405 || $status === 0) {
            // Fall back to a ranged GET (first 256 bytes is plenty to confirm
            // the object exists and to read magic-byte content-type).
            [$status, $type, $errno] = $this->probeRequest($url, head: false);
        }
        $isImage = str_starts_with(strtolower($type), 'image/');
        return [
            'ok'           => $status === 200 && $isImage,
            'http_status'  => $status,
            'content_type' => $type,
            'curl_errno'   => $errno,
        ];
    }

    /** @return array{0:int,1:string,2:int} */
    private function probeRequest(string $url, bool $head): array
    {
        $ch = curl_init($url);
        $opts = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => self::TIMEOUT_SECONDS,
            CURLOPT_CONNECTTIMEOUT => 4,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 3,
            CURLOPT_USERAGENT      => 'CertiCarsPDF-validator/1.0',
            CURLOPT_NOBODY         => $head,
        ];
        if (!$head) {
            // Ranged GET — pulls just the head of the object so MIME sniff
            // succeeds without burning bandwidth.
            $opts[CURLOPT_HTTPHEADER] = ['Range: bytes=0-255'];
        }
        curl_setopt_array($ch, $opts);
        curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $type   = (string) curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        $errno  = curl_errno($ch);
        curl_close($ch);
        return [$status, $type, $errno];
    }

    private function skip(string $path, string $context, string $reason): ?string
    {
        $this->stats['skipped']++;
        return null;
    }
}
