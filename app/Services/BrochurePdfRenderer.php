<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Spatie\Browsershot\Browsershot;
use Spatie\Browsershot\Exceptions\CouldNotTakeBrowsershot;

/**
 * Browser-driven PDF rendering for the public CertiCheck brochure.
 *
 * Why this exists:
 * DomPDF cannot reliably handle remote/R2 image fetches, modern CSS layout,
 * or controlled page breaks. The previous PdfImageEmbedder service worked
 * around the image gap, but DomPDF's layout fidelity is still capped — large
 * empty gray blocks, orphaned headers, no real flexbox. This renderer drives
 * a headless Chromium so the brochure HTML renders the same way it would in
 * a real browser: images loaded directly from R2, CSS as written, proper
 * page breaks, real font rendering.
 *
 * Failure model:
 * If Chromium is missing, OOM, or the render times out, render() throws a
 * RuntimeException. The caller (PdfController) catches it and falls back to
 * the DomPDF + PdfImageEmbedder path so the customer still gets a PDF.
 */
class BrochurePdfRenderer
{
    /**
     * Hard cap for a single render. Brochures with many photos can take a
     * few seconds; anything past this is almost certainly a stuck process.
     */
    private const TIMEOUT_SECONDS = 60;

    /**
     * Render the given pre-built HTML to PDF bytes. The HTML must already
     * contain absolute, browser-reachable image URLs (R2 public URLs).
     */
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

            // Honour an explicit Chromium binary when the OS/env points at one.
            // Production Docker sets PUPPETEER_EXECUTABLE_PATH=/usr/bin/chromium;
            // local dev can export it to point at /Applications/Google Chrome.app/...
            $chromiumPath = env('PUPPETEER_EXECUTABLE_PATH');
            if (is_string($chromiumPath) && $chromiumPath !== '' && is_executable($chromiumPath)) {
                $shot->setChromePath($chromiumPath);
            }

            $bytes = $shot->pdf();
        } catch (CouldNotTakeBrowsershot $e) {
            // Chromium itself failed (missing, crashed, timed out, OOM…).
            // Surface as a single RuntimeException so the caller can decide
            // whether to fall back. Don't try to recover here.
            Log::warning('pdf.browsershot.failed', [
                'report_id' => $reportId,
                'exception' => get_class($e),
                'message'   => $e->getMessage(),
            ]);
            throw new \RuntimeException(
                'Browsershot render failed: ' . $e->getMessage(),
                0,
                $e
            );
        }

        Log::info('pdf.browsershot.ok', [
            'report_id'    => $reportId,
            'duration_ms'  => (int) ((microtime(true) - $start) * 1000),
            'bytes'        => strlen($bytes),
        ]);

        return $bytes;
    }
}
