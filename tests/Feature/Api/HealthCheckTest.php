<?php

/**
 * Cel: Smoke tests dla podstawowych endpointów API
 * Moduł: Tests
 * Odpowiedzialny: Claude-Code
 * Data: 2025-09-02
 */

namespace Tests\Feature\Api;

use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    public function test_health_endpoint_returns_success()
    {
        $response = $this->getJson('/api/health');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'timestamp',
                'version',
            ])
            ->assertJson([
                'status' => 'ok',
            ]);
    }

    public function test_api_endpoints_return_appropriate_status_codes()
    {
        // Test unauthenticated access to protected endpoints
        $this->getJson('/api/user')->assertStatus(401);

        // Test rate limiting doesn't block normal requests
        for ($i = 0; $i < 5; $i++) {
            $response = $this->getJson('/api/health');
            $response->assertStatus(200);
        }
    }

    public function test_cors_headers_are_present()
    {
        $response = $this->options('/api/health', [], [
            'Origin' => 'http://localhost:3000',
            'Access-Control-Request-Method' => 'GET',
        ]);

        $response->assertHeader('Access-Control-Allow-Origin');
        $response->assertHeader('Access-Control-Allow-Methods');
    }

    public function test_security_headers_are_present()
    {
        $response = $this->getJson('/api/health');

        $response->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-XSS-Protection', '1; mode=block')
            ->assertHeader('Server', 'SkyBrokerSystem');
    }
}
