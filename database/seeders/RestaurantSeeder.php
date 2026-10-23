<?php

namespace Database\Seeders;

use App\Models\Restaurant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RestaurantSeeder extends Seeder
{
    public function run(): void
    {
        Restaurant::firstOrCreate(
            ['email' => 'pembachya@merotable.com'],
            [
                'name' => 'Pemba Chya',
                'owner_name' => 'Pemba Tamang',
                'slug' => Str::slug('Pemba Chya') . '-pembachya',
                'password' => Hash::make('1234567890'),
                'contact_number' => '9800000000',
                'description' => 'Welcome to Pemba Chya.',
                'address' => 'Kathmandu, Nepal',
                'logo' => null,
            ]
        );
    }
}
