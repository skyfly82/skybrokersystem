<?php

/**
 * Cel: Smoke tests dla endpointów uwierzytelniania
 * Moduł: Tests
 * Odpowiedzialny: sky_fly82
 * Data: 2025-09-02
 */

namespace Tests\Feature\Api;

use App\Models\Customer;
use App\Models\CustomerUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_endpoint_validates_input()
    {
        // Test missing email
        $response = $this->postJson('/api/auth/login', [
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);

        // Test missing password
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_register_endpoint_validates_input()
    {
        $customer = Customer::factory()->create();

        // Test missing required fields
        $response = $this->postJson('/api/auth/register', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['customer_id', 'name', 'email', 'password']);

        // Test invalid email
        $response = $this->postJson('/api/auth/register', [
            'customer_id' => $customer->id,
            'name' => 'Test User',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_rate_limiting_on_auth_endpoints()
    {
        // Make multiple failed login attempts to trigger rate limiting
        for ($i = 0; $i < 6; $i++) {
            $response = $this->postJson('/api/auth/login', [
                'email' => 'nonexistent@example.com',
                'password' => 'wrongpassword',
            ]);

            if ($i < 5) {
                // First 5 attempts should return validation error or 401
                $this->assertContains($response->status(), [401, 422]);
            } else {
                // 6th attempt should be rate limited
                $response->assertStatus(429);
            }
        }
    }

    public function test_successful_login_returns_token()
    {
        $customer = Customer::factory()->create(['status' => 'active']);
        $user = CustomerUser::factory()->create([
            'customer_id' => $customer->id,
            'password' => bcrypt('password123'),
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'user',
                    'token',
                    'token_type',
                ],
            ]);
    }
}
