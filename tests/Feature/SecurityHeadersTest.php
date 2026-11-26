<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_send_a_nonce_based_content_security_policy(): void
    {
        $response = $this->get(route('home'))->assertOk();

        $policy = $response->headers->get('Content-Security-Policy');
        $this->assertMatchesRegularExpression("/script-src 'self' 'nonce-[A-Za-z0-9]+'/", $policy);
        $this->assertStringContainsString("frame-ancestors 'none'", $policy);
        $this->assertStringContainsString("object-src 'none'", $policy);
        $this->assertStringNotContainsString('unsafe-inline', $policy);

        $response
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeaderMissing('X-Robots-Tag');
    }

    public function test_hsts_is_only_sent_over_https(): void
    {
        $this->get(route('home'))->assertHeaderMissing('Strict-Transport-Security');

        $this->get('https://localhost/')->assertHeader('Strict-Transport-Security', 'max-age=31536000');
    }
}
