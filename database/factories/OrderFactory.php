<?php

namespace Database\Factories;

use App\Enums\OrderStatusesEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'status' => fake()->randomElement(OrderStatusesEnum::cases())->value,
            'total' => number_format(fake()->randomFloat(2, 0, 9999.99), 2, '.', ''),
        ];
    }
}
