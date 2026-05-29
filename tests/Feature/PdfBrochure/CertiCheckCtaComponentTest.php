<?php

namespace Tests\Feature\PdfBrochure;

use Tests\TestCase;

/**
 * The frontend pill MUST hide the download icon (and the href, and the
 * download attribute) when the brochure isn't ready. Otherwise the user
 * clicks a link that leads to a 404 and Chrome saves something it
 * shouldn't.
 */
class CertiCheckCtaComponentTest extends TestCase
{
    /** Strip the once-style block so assertions test the live markup only. */
    private function markupOnly(string $html): string
    {
        return preg_replace('#<style.*?</style>#s', '', $html) ?? '';
    }

    public function test_ready_state_renders_a_link_with_download_attribute(): void
    {
        $html = $this->markupOnly(view('components.certicheck-cta', [
            'slug'  => 'audi-a4',
            'ready' => true,
            'size'  => 'md',
        ])->render());

        $this->assertStringContainsString('<a', $html);
        $this->assertStringContainsString('href=', $html);
        $this->assertStringContainsString('download="download"', $html,
            'ready state must emit a real <a download> link');
        $this->assertStringContainsString('cs-certi-cta-trailing', $html,
            'download icon must be present in the ready state');
        $this->assertStringNotContainsString('cs-certi-cta--pending', $html);
    }

    public function test_pending_state_renders_a_span_with_no_link_no_download(): void
    {
        $html = $this->markupOnly(view('components.certicheck-cta', [
            'slug'  => 'audi-a4',
            'ready' => false,
            'size'  => 'md',
        ])->render());

        // No anchor → no clickable affordance → no broken download attempt.
        $this->assertStringNotContainsString('<a ', $html);
        $this->assertStringNotContainsString('href=', $html);
        $this->assertStringNotContainsString('download="download"', $html);
        $this->assertStringNotContainsString('cs-certi-cta-trailing', $html,
            'download icon must NOT be present in the pending state');
        $this->assertStringContainsString('cs-certi-cta--pending', $html);
        $this->assertStringContainsString('CertiCheck', $html);
    }

    public function test_pending_is_default_so_callers_must_opt_in(): void
    {
        // If a caller forgets to pass :ready, the safe default is "not
        // ready" → pending pill. Belt-and-braces guarantee against new
        // callsites accidentally exposing a broken link.
        $html = $this->markupOnly(view('components.certicheck-cta', [
            'slug' => 'audi-a4',
            'size' => 'md',
        ])->render());
        $this->assertStringContainsString('cs-certi-cta--pending', $html);
        $this->assertStringNotContainsString('download="download"', $html);
        $this->assertStringNotContainsString('<a ', $html);
    }
}
