<?php

namespace Tests\Feature;

use App\Enums\RolesEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $user;
    protected string $adminToken;
    protected string $userToken;

    protected function setUp(): void {
        parent::setUp();

        $this->admin = User::factory()->create()->assignRole(RolesEnum::ADMIN->value);
        $this->adminToken = $this->admin->createToken('auth-test-token')->plainTextToken;

        $this->user = User::factory()->create()->assignRole(RolesEnum::USER->value);
        $this->userToken = $this->user->createToken('auth-test-token')->plainTextToken;
    }

    /**
     * Product model CRUD test
     * Index method
     */
    public function test_user_can_view_products(): void
    {
        $response = $this->getJson('/api/products');

        $response->assertStatus(200);
    }

    /**
     * Create method
     */
    public function test_admin_can_create_product(): void
    {
        $response = $this->withToken($this->adminToken)
            ->postJson('/api/products', [
                'name' => fake()->word(),
                'description' => fake()->sentence(),
                'price' => fake()->randomFloat(2),
                'stock' => fake()->randomNumber(2, true),
            ]);

        $response->assertStatus(201);
    }

    public function test_user_cannot_create_product(): void
    {
        $response = $this->withToken($this->userToken)
            ->postJson('/api/products', [
                'name' => fake()->word(),
                'description' => fake()->sentence(),
                'price' => fake()->randomFloat(2),
                'stock' => fake()->randomNumber(2, true),
            ]);

        $response->assertForbidden();
    }

    /**
     * Show method
     */
    public function test_user_can_view_single_product(): void
    {
        $response = $this->getJson('/api/products/1');

        $response->assertStatus(200);
    }

    /**
     * Update method
     */
    public function test_admin_can_update_product(): void
    {
        $response = $this->withToken($this->adminToken)
            ->putJson('/api/products/1', [
                'name' => fake()->word(),
                'description' => fake()->sentence(),
                'price' => fake()->randomFloat(2),
                'stock' => fake()->randomNumber(2, true),
            ]);

        $response->assertStatus(200);
    }

    public function test_user_cannot_update_product(): void
    {
        $response = $this->withToken($this->userToken)
            ->putJson('/api/products/1', [
                'name' => fake()->word(),
                'description' => fake()->sentence(),
                'price' => fake()->randomFloat(2),
                'stock' => fake()->randomNumber(2, true),
            ]);

        $response->assertForbidden();
    }

    /**
     * Delete method
     */
    public function test_admin_can_delete_product(): void {
        $response = $this->withToken($this->adminToken)
            ->deleteJson('/api/products/1');

        $response->assertStatus(200);
    }

    public function test_user_cannot_delete_product(): void {
        $response = $this->withToken($this->userToken)
            ->deleteJson('/api/products/1');

        $response->assertForbidden();
    }
}
