<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if admin user already exists
        if (User::where('email', 'admin@telegram.shop')->exists()) {
            return;
        }

        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@telegram.shop',
            'email_verified_at' => now(),
            'password' => Hash::make('password'), // Change this in production!
            'remember_token' => Str::random(10),
        ]);
    }
}
