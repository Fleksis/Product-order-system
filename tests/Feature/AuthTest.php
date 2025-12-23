<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Tests registration flow
     */
    public function test_register(): void
    {
        $password = fake()->password(8);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/register', [
            'firstname' => fake()->firstname(),
            'lastname' => fake()->lastName(),
            'email' => fake()->email(),
            'password' => $password,
            'password_confirmation' => $password,
        ]);

        $response->assertStatus(200);
    }
}
