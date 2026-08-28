<?php

namespace Tests\Feature\Http;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_responses_include_the_expected_security_headers(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()')
            ->assertHeader('Cross-Origin-Opener-Policy', 'same-origin')
            ->assertHeader('Cross-Origin-Resource-Policy', 'same-origin')
            ->assertHeader('X-Permitted-Cross-Domain-Policies', 'none');

        $this->assertStringContainsString(
            "object-src 'none'",
            (string) $this->get('/')->headers->get('Content-Security-Policy'),
        );
        $this->assertStringNotContainsString(
            "script-src 'self' 'unsafe-inline'",
            (string) $this->get('/')->headers->get('Content-Security-Policy'),
        );
    }

    public function test_https_responses_include_hsts(): void
    {
        $this->withHeader('X-Forwarded-Proto', 'https')
            ->get('/')
            ->assertHeader(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains',
            );
    }
}
