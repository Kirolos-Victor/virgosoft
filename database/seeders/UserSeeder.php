<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Alice Trader',
            'email' => 'alice@example.com',
            'password' => bcrypt('password'),
            'balance' => '100000.00000000',
        ]);

        User::create([
            'name' => 'Bob Investor',
            'email' => 'bob@example.com',
            'password' => bcrypt('password'),
            'balance' => '50000.00000000',
        ]);

        User::create([
            'name' => 'Charlie Crypto',
            'email' => 'charlie@example.com',
            'password' => bcrypt('password'),
            'balance' => '75000.00000000',
        ]);
    }
}
