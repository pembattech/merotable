<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class SuperAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Prevent duplicate root user
        if (User::where('email', 'root@merotable.com')->exists()) {
            return;
        }

        User::create([
            'name' => 'Root Admin',
            'email' => 'root@merotable.com',
            'password' => Hash::make('root1234'),
            'role' => 'root',
            'restaurant_id' => null,
            'phone' => null,
            'is_active' => true,
        ]);
    }
}
