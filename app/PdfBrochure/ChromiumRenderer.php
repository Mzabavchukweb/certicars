<?php

namespace App\PdfBrochure;

use Illuminate\Support\Facades\Log;
use Spatie\Browsershot\Browsershot;

/**
 * Headless-Chromium PDF renderer (via spatie/browsershot).
 *
 * All images embedded in the HTML are already base64 `data:` URIs (see
 * ImageEmbedder) so Chromium makes ZERO network requests during render —
 * with no network involved the only remaining failure surface is the
 * Chromium binary itself. assertReady() pre-checks the binary so callers
 * can fail fast (clean 500 JSON to the customer) instead of waiting for
 * Browsershot's subprocess to time out.
 *
 * render() catches every Throwable from Browsershot — not just the
 * library-specific CouldNotTakeBrowsershot, but also Symfony's
 * ProcessFailedException, ProcessTimedOutException, and anything else
 * the bundled wrapper might emit — and wraps them as
 * BrochureRenderException so the caller has exactly one exception type
 * to handle.
 */
// Not final: tests mock it to avoid spawning a real Chromium process.
class ChromiumRenderer
{
    private const TIMEOUT_SECONDS = 60;

    /**
     * Pre-flight: confirm the Chromium binary we plan to use is actually
     * executable. Avoid the 60-second Browsershot timeout when the binary
     * is missing on a deploy. Throws BrochureRenderException if not OK.
     */
    public function assertReady(string $reportId): void
    {
        $path = $this->resolveChromiumPath();
        if ($path === null) {
            Log::error('pdf_brochure.chromium_not_ready', [
                'report_id' => $reportId,
                'reason'    => 'no_binary_found',
                'env_path'  => env('PUPPETEER_EXECUTABLE_PATH'),
            ]);
            throw new BrochureRenderException(
                'Chromium binary not found. Check PUPPETEER_EXECUTABLE_PATH or install /usr/bin/chromium.'
            );
        }
        Log::info('pdf_brochure.chromium_ready', [
            'report_id' => $reportId,
            'path'      => $path,
        ]);
    }

    public function render(string $html, string $reportId): string
    {
        $start = microtime(true);
        $chromiumPath = $this->resolveChromiumPath();

        try {
            $shot = Browsershot::html($html)
                ->format('A4')
                ->margins(12, 10, 14, 10)
                ->showBackground()
                ->waitUntilNetworkIdle()
                ->timeout(self::TIMEOUT_SECONDS)
                ->noSandbox()
                ->emulateMedia('print');

            if ($chromiumPath !== null) {
                $shot->setChromePath($chromiumPath);
            }

            $bytes = $shot->pdf();
        } catch (\Throwable $e) {
            // Catch EVERYTHING — Browsershot's API throws
            // CouldNotTakeBrowsershot, but its internals throw Symfony
            // ProcessFailedException / ProcessTimedOutException. Production
            // hit the latter and they were never caught.
            Log::error('pdf_brochure.chromium_failed', [
                'report_id' => $reportId,
                'exception' => get_class($e),
                'message'   => $e->getMessage(),
                'path'      => $chromiumPath,
            ]);
            throw new BrochureRenderException(
                'Chromium failed to render the brochure: ' . $e->getMessage(),
                0,
                $e
            );
        }

        if (!is_string($bytes) || strlen($bytes) < 5 || substr($bytes, 0, 4) !== '%PDF') {
            // Browsershot returned something that isn't a PDF. This was
            // never observed locally but: belt-and-braces against shipping
            // bytes that Chrome will save as a broken file.
            Log::error('pdf_brochure.chromium_bad_output', [
                'report_id'    => $reportId,
                'bytes_length' => is_string($bytes) ? strlen($bytes) : 0,
                'head_hex'     => is_string($bytes) ? bin2hex(substr($bytes, 0, 16)) : '',
            ]);
            throw new BrochureRenderException('Chromium produced a non-PDF response.');
        }

        Log::info('pdf_brochure.chromium_ok', [
            'report_id'   => $reportId,
            'duration_ms' => (int) ((microtime(true) - $start) * 1000),
            'bytes'       => strlen($bytes),
        ]);

        return $bytes;
    }

    /**
     * Resolve the Chromium binary to use. Order of preference:
     *  1. PUPPETEER_EXECUTABLE_PATH env (production Docker)
     *  2. /usr/bin/chromium  (Debian package shipped in the image)
     *  3. /usr/bin/chromium-browser  (some Ubuntu variants)
     *  4. macOS-bundled Chrome (local dev)
     *
     * Returns null if no candidate is executable.
     */
    private function resolveChromiumPath(): ?string
    {
        $env = env('PUPPETEER_EXECUTABLE_PATH');
        $candidates = [
            is_string($env) && $env !== '' ? $env : null,
            '/usr/bin/chromium',
            '/usr/bin/chromium-browser',
            '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',
        ];
        foreach ($candidates as $c) {
            if (is_string($c) && $c !== '' && is_executable($c)) {
                return $c;
            }
        }
        return null;
    }
}
