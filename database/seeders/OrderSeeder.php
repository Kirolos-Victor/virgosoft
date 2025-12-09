<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $alice = User::where('email', 'alice@example.com')->first();
        $bob = User::where('email', 'bob@example.com')->first();
        $charlie = User::where('email', 'charlie@example.com')->first();

        Order::create([
            'user_id' => $alice->id,
            'symbol' => 'BTC',
            'side' => 'buy',
            'price' => '45000.00000000',
            'amount' => '0.50000000',
            'filled_amount' => '0.00000000',
            'status' => Order::STATUS_OPEN,
        ]);

        Order::create([
            'user_id' => $bob->id,
            'symbol' => 'BTC',
            'side' => 'sell',
            'price' => '46000.00000000',
            'amount' => '0.30000000',
            'filled_amount' => '0.00000000',
            'status' => Order::STATUS_OPEN,
        ]);

        Order::create([
            'user_id' => $charlie->id,
            'symbol' => 'BTC',
            'side' => 'buy',
            'price' => '44500.00000000',
            'amount' => '1.00000000',
            'filled_amount' => '0.00000000',
            'status' => Order::STATUS_OPEN,
        ]);

        Order::create([
            'user_id' => $alice->id,
            'symbol' => 'ETH',
            'side' => 'sell',
            'price' => '3000.00000000',
            'amount' => '5.00000000',
            'filled_amount' => '0.00000000',
            'status' => Order::STATUS_OPEN,
        ]);

        Order::create([
            'user_id' => $bob->id,
            'symbol' => 'ETH',
            'side' => 'buy',
            'price' => '2950.00000000',
            'amount' => '10.00000000',
            'filled_amount' => '0.00000000',
            'status' => Order::STATUS_OPEN,
        ]);
    }
}
