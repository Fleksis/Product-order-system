<?php

namespace Tests\Feature;

use App\Enums\OrderStatusesEnum;
use App\Enums\RolesEnum;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $user;
    protected string $adminToken;
    protected string $userToken;

    protected array $orderProducts = [
        [
            "id" => 11,
            "quantity" => 1,
        ],
        [
            "id" => 22,
            "quantity" => 2,
        ],
    ];

    protected array $updateOrderProducts = [
        11 => [
            'price' => 20.00,
            'quantity' => 2,
        ],
        21 => [
            'price' => 40.00,
            'quantity' => 4,
        ]
    ];

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
    public function test_admin_can_view_orders(): void
    {
        $response = $this->withToken($this->adminToken)->getJson('/api/orders');

        $response->assertStatus(200);
    }

    public function test_user_cannot_view_orders(): void
    {
        $response = $this->withToken($this->userToken)->getJson('/api/orders');

        $response->assertForbidden();
    }

    public function test_admin_can_view_own_orders(): void {
        $response = $this->withToken($this->adminToken)->getJson('/api/user-orders');

        $response->assertStatus(200);
    }

    public function test_user_can_view_own_orders(): void {
        $response = $this->withToken($this->userToken)->getJson('/api/user-orders');

        $response->assertStatus(200);
    }

    public function test_guest_cannot_view_order(): void
    {
        $response = $this->getJson('/api/orders');

        $response->assertUnauthorized();
    }

    /**
     * Create method
     */
    public function test_admin_can_create_order(): void
    {
        $response = $this->withToken($this->adminToken)->postJson('/api/orders', [
            'products' => $this->orderProducts,
        ]);

        $response->assertStatus(200);
    }

    public function test_user_can_create_order(): void
    {
        $response = $this->withToken($this->adminToken)->postJson('/api/orders', [
            'products' => $this->orderProducts,
        ]);

        $response->assertStatus(200);
    }

    /**
     * Show method
     */
    public function test_admin_can_view_order(): void {

        $order = Order::factory()->create();

        $response = $this->withToken($this->adminToken)->getJson("/api/orders/$order->id");

        $response->assertStatus(200);
    }

    public function test_user_can_view_order(): void {

        $order = Order::factory()->for($this->user)->create();

        $response = $this->withToken($this->userToken)->getJson("/api/orders/$order->id");

        $response->assertStatus(200);
    }

    public function test_user_cannot_view_other_user_order(): void {

        $order = Order::factory()->create();

        $response = $this->withToken($this->userToken)->getJson("/api/orders/$order->id");

        $response->assertForbidden();
    }

    /**
     * Update method
     */
    public function test_admin_can_update_order(): void
    {
        $order = Order::factory()->create(['status' => OrderStatusesEnum::CREATED->value]);
        $order->products()->attach($this->updateOrderProducts);

        $response = $this->withToken($this->adminToken)->putJson("/api/orders/$order->id", [
            'products' => $this->orderProducts,
        ]);

        $response->assertStatus(200);
    }

    public function test_user_cannot_update_order(): void
    {
        $order = Order::factory()->create(['status' => OrderStatusesEnum::CREATED->value]);
        $order->products()->attach($this->updateOrderProducts);

        $response = $this->withToken($this->userToken)->putJson("/api/orders/$order->id", [
            'products' => $this->orderProducts,
        ]);
        $response->assertForbidden();
    }

    /**
     * Destroy method
     */
    public function test_admin_can_delete_order(): void
    {
        $order = Order::factory()->create();

        $response = $this->withToken($this->adminToken)->deleteJson("/api/orders/$order->id");

        $response->assertStatus(200);
    }

    public function test_admin_cannot_delete_order(): void
    {
        $order = Order::factory()->create();

        $response = $this->withToken($this->adminToken)->deleteJson("/api/orders/$order->id");

        $response->assertStatus(200);
    }

    /**
     * Cancel order method
     */
    public function test_admin_can_cancel_any_order(): void
    {
        $order = Order::factory()->create(['status' => OrderStatusesEnum::CREATED->value]);
        $order->products()->attach($this->updateOrderProducts);

        $response = $this->withToken($this->adminToken)->getJson("/api/cancel-order/$order->id");
        $response->assertStatus(200);
    }

    public function test_user_can_cancel_own_order(): void
    {
        $order = Order::factory()->for($this->user)->create(['status' => OrderStatusesEnum::CREATED->value]);
        $order->products()->attach($this->updateOrderProducts);

        $response = $this->withToken($this->userToken)->getJson("/api/cancel-order/$order->id");
        $response->assertStatus(200);
    }

    public function test_user_cannot_cancel_other_user_order(): void
    {
        $order = Order::factory()->create(['status' => OrderStatusesEnum::CREATED->value]);
        $order->products()->attach($this->updateOrderProducts);

        $response = $this->withToken($this->userToken)->getJson("/api/cancel-order/$order->id");
        $response->assertForbidden();
    }

    /**
     * Complete order method
     */
    public function test_admin_can_complete_order(): void
    {
        $order = Order::factory()->create(['status' => OrderStatusesEnum::CREATED->value]);
        $order->products()->attach($this->updateOrderProducts);

        $response = $this->withToken($this->adminToken)->getJson("/api/complete-order/$order->id");
        $response->assertStatus(200);
    }

    public function test_user_cannot_complete_order(): void
    {
        $order = Order::factory()->create(['status' => OrderStatusesEnum::CREATED->value]);
        $order->products()->attach($this->updateOrderProducts);

        $response = $this->withToken($this->userToken)->getJson("/api/complete-order/$order->id");
        $response->assertForbidden();
    }
}
