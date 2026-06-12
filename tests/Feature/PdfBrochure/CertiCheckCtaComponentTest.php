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
            'slug'   => 'audi-a4',
            'status' => 'ready',
            'size'   => 'md',
        ])->render());

        $this->assertStringContainsString('<a', $html);
        $this->assertStringContainsString('href=', $html);
        $this->assertStringContainsString('download="download"', $html,
            'ready state must emit a real <a download> link');
        $this->assertStringContainsString('cs-certi-cta-trailing', $html,
            'download icon must be present in the ready state');
        $this->assertStringContainsString('Pobierz PDF', $html,
            'ready state on md variant must show explicit „Pobierz PDF" label so customers know what the click does');
        $this->assertStringNotContainsString('cs-certi-cta--pending', $html);
    }

    public function test_processing_state_renders_clickable_button_that_opens_modal(): void
    {
        $html = $this->markupOnly(view('components.certicheck-cta', [
            'slug'   => 'audi-a4',
            'status' => 'processing',
            'size'   => 'md',
        ])->render());

        // No anchor — never a broken download attempt.
        $this->assertStringNotContainsString('href=', $html);
        $this->assertStringNotContainsString('download="download"', $html);
        // But a real <button> so click opens the premium pending modal.
        $this->assertStringContainsString('<button', $html);
        $this->assertStringContainsString('csOpenCertiCheckPending', $html);
        $this->assertStringContainsString('cs-certi-cta--pending', $html);
        $this->assertStringContainsString('CertiCheck', $html);
    }

    public function test_failed_and_missing_states_also_render_pending_button(): void
    {
        foreach (['failed', 'missing'] as $status) {
            $html = $this->markupOnly(view('components.certicheck-cta', [
                'slug'   => 'audi-a4',
                'status' => $status,
                'size'   => 'md',
            ])->render());
            $this->assertStringContainsString('<button', $html,
                "status=$status must render the pending button (never a dead link)");
            $this->assertStringNotContainsString('download="download"', $html,
                "status=$status must NOT expose a download link");
            $this->assertStringContainsString('cs-certi-cta--pending', $html);
        }
    }

    public function test_missing_is_default_so_callers_must_opt_in(): void
    {
        // If a caller forgets to pass :status, the safe default is "missing"
        // → pending button + modal. Belt-and-braces guarantee against new
        // callsites accidentally exposing a broken link.
        $html = $this->markupOnly(view('components.certicheck-cta', [
            'slug' => 'audi-a4',
            'size' => 'md',
        ])->render());
        $this->assertStringContainsString('cs-certi-cta--pending', $html);
        $this->assertStringNotContainsString('download="download"', $html);
        $this->assertStringNotContainsString('<a ', $html);
        $this->assertStringContainsString('<button', $html);
    }
}
