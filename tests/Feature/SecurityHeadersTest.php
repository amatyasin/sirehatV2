<?php

namespace Tests\Feature;

use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    /**
     * Test that required security headers are present in web responses.
     */
    public function test_security_headers_are_present(): void
    {
        $response = $this->get('/');

        // 1. Legacy XSS Protection
        $response->assertHeader('X-XSS-Protection', '1; mode=block');

        // 2. Content Security Policy
        $this->assertTrue($response->headers->has('Content-Security-Policy'));
        $this->assertStringContainsString("default-src 'self'", (string) $response->headers->get('Content-Security-Policy'));

        // 3. X-Frame-Options
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');

        // 4. X-Content-Type-Options
        $response->assertHeader('X-Content-Type-Options', 'nosniff');

        // 5. Referrer-Policy
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');

        // 6. Permissions-Policy
        $response->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
    }

    /**
     * Test that cookie security configuration options are set properly.
     */
    public function test_cookie_security_config(): void
    {
        $this->assertTrue(config('session.http_only'));
        $this->assertEquals('lax', config('session.same_site'));
    }
}
