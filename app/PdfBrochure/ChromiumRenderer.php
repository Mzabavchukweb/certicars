<?php

namespace App\PdfBrochure;

use Illuminate\Support\Facades\Log;
use Spatie\Browsershot\Browsershot;
use Spatie\Browsershot\Exceptions\CouldNotTakeBrowsershot;

/**
 * Headless-Chromium PDF renderer (via spatie/browsershot).
 *
 * All images embedded in the HTML are already base64 `data:` URIs (see
 * ImageEmbedder) so Chromium makes ZERO network requests during render —
 * that was the source of the previous "PDF says 42 photos but the pages are
 * blank" failure mode where HEAD-validated URLs failed Chromium's actual
 * GET. With no network involved the only remaining failure surface is the
 * Chromium binary itself (missing, OOM, fontconfig…) which throws
 * BrochureRenderException so the caller knows to surface a 500 rather than
 * ship a corrupt PDF.
 */
// Not final: tests need to mock it for route assertions without spawning a
// real Chromium process. Subclasses elsewhere are not part of the contract.
class ChromiumRenderer
{
    private const TIMEOUT_SECONDS = 60;

    public function render(string $html, string $reportId): string
    {
        $start = microtime(true);

        try {
            $shot = Browsershot::html($html)
                ->format('A4')
                ->margins(12, 10, 14, 10)
                ->showBackground()
                ->waitUntilNetworkIdle()
                ->timeout(self::TIMEOUT_SECONDS)
                ->noSandbox()
                ->emulateMedia('print');

            // Honour an explicit Chromium binary when the OS/env points at
            // one. Production Docker sets PUPPETEER_EXECUTABLE_PATH=
            // /usr/bin/chromium; local dev can export it to point at the
            // macOS Chrome binary.
            $chromiumPath = env('PUPPETEER_EXECUTABLE_PATH');
            if (is_string($chromiumPath) && $chromiumPath !== '' && is_executable($chromiumPath)) {
                $shot->setChromePath($chromiumPath);
            }

            $bytes = $shot->pdf();
        } catch (CouldNotTakeBrowsershot $e) {
            Log::error('pdf_brochure.chromium_failed', [
                'report_id' => $reportId,
                'exception' => get_class($e),
                'message'   => $e->getMessage(),
            ]);
            throw new BrochureRenderException(
                'Chromium failed to render the brochure: ' . $e->getMessage(),
                0,
                $e
            );
        }

        Log::info('pdf_brochure.chromium_ok', [
            'report_id'   => $reportId,
            'duration_ms' => (int) ((microtime(true) - $start) * 1000),
            'bytes'       => strlen($bytes),
        ]);

        return $bytes;
    }
}
