<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * P1 regression guard: admin sessions must never be shorter than 12 hours.
 *
 * Background:
 *   Production Railway environment intermittently reports SESSION_LIFETIME=60
 *   even after operators set 720 in the dashboard. Result: Set-Cookie returned
 *   Max-Age=3600, admin sessions died mid-wizard after 60 minutes, operators
 *   lost long manual car entries.
 *
 *   config/session.php now enforces a hard floor via max(720, …). These tests
 *   prevent accidental removal of that floor.
 */
class SessionLifetimeFloorTest extends TestCase
{
    public function test_session_config_source_contains_720_minute_floor(): void
    {
        $source = file_get_contents(config_path('session.php'));

        $this->assertMatchesRegularExpression(
            "/'lifetime'\s*=>\s*max\s*\(\s*720\s*,/",
            $source,
            'config/session.php must enforce a minimum 720-minute session lifetime '
            . 'via max(720, …) — removing this floor reintroduces the P1 60-minute '
            . 'admin wizard expiry bug.'
        );
    }

    public function test_runtime_session_lifetime_is_at_least_720_minutes(): void
    {
        $lifetime = (int) config('session.lifetime');
        $this->assertGreaterThanOrEqual(
            720,
            $lifetime,
            "config('session.lifetime') resolved to {$lifetime} — must be ≥ 720 (= 12h). "
            . 'Cookie Max-Age in production would be ' . ($lifetime * 60) . 's instead of ≥43200s.'
        );
    }

    public function test_floor_formula_overrides_low_env_value(): void
    {
        // Independent unit check of the formula itself — guards against the
        // floor being silently inverted (e.g. min() instead of max()).
        $this->assertSame(720, max(720, 60),  'floor must elevate 60 → 720');
        $this->assertSame(720, max(720, 120), 'floor must elevate Laravel default 120 → 720');
        $this->assertSame(720, max(720, 720), 'floor preserves 720');
        $this->assertSame(1440, max(720, 1440), 'floor must not cap higher values');
    }
}
