<?php

namespace Tests\Feature;

use App\Enums\RolesEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    /**
     * Tests registration flow
     */
    public function test_user_can_register(): void
    {
        $password = fake()->password(8);

        $response = $this->postJson('/api/register', [
            'firstname' => fake()->firstname(),
            'lastname' => fake()->lastName(),
            'email' => fake()->email(),
            'password' => $password,
            'password_confirmation' => $password,
        ]);

        $response->assertStatus(200);
    }

    /**
     * Tests login flow
     */
    public function test_user_can_login(): void
    {
        $password = fake()->password(8);

        $user = User::factory()->create([
            'password' => $password,
        ])->assignRole(RolesEnum::USER->value);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertStatus(200);
    }

    public function test_user_cannot_login_with_wrong_email(): void {
        $response = $this->postJson('/api/login', [
            'email' => 'test@test.test',
            'password' => 'Test123',
        ]);

        $response->assertUnauthorized();
    }

    public function test_user_cannot_login_with_wrong_password(): void {
        $user = User::factory()->create()->assignRole(RolesEnum::USER->value);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'Test123',
        ]);

        $response->assertUnauthorized();
    }
}
