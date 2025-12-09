<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\User;
use Illuminate\Database\Seeder;

class AssetSeeder extends Seeder
{
    public function run(): void
    {
        $alice = User::where('email', 'alice@example.com')->first();
        $bob = User::where('email', 'bob@example.com')->first();
        $charlie = User::where('email', 'charlie@example.com')->first();

        Asset::create([
            'user_id' => $alice->id,
            'symbol' => 'BTC',
            'amount' => '10.50000000',
            'locked_amount' => '0.00000000',
        ]);

        Asset::create([
            'user_id' => $alice->id,
            'symbol' => 'ETH',
            'amount' => '50.00000000',
            'locked_amount' => '0.00000000',
        ]);

        Asset::create([
            'user_id' => $bob->id,
            'symbol' => 'BTC',
            'amount' => '5.25000000',
            'locked_amount' => '0.00000000',
        ]);

        Asset::create([
            'user_id' => $bob->id,
            'symbol' => 'ETH',
            'amount' => '100.00000000',
            'locked_amount' => '0.00000000',
        ]);

        Asset::create([
            'user_id' => $charlie->id,
            'symbol' => 'BTC',
            'amount' => '8.00000000',
            'locked_amount' => '0.00000000',
        ]);
    }
}
