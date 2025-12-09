<?php

namespace Database\Factories;

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
            'user_id' => \App\Models\User::factory(),
            'symbol' => fake()->randomElement(['BTC', 'ETH', 'USDT']),
            'side' => fake()->randomElement(['buy', 'sell']),
            'price' => fake()->randomFloat(8, 1000, 100000),
            'amount' => fake()->randomFloat(8, 0.01, 10),
            'filled_amount' => '0.00000000',
            'status' => \App\Models\Order::STATUS_OPEN,
        ];
    }
}
