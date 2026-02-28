<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AddOn;

class AddOnSeeder extends Seeder
{
    public function run(): void
    {
        $addOns = [
            [
                'name' => 'Extra Tables',
                'feature_key' => 'tables_limit',
                'price' => 300,
            ],
            [
                'name' => 'Extra Staff',
                'feature_key' => 'staff_limit',
                'price' => 200,
            ],
        ];

        foreach ($addOns as $addOn) {
            AddOn::updateOrCreate(
                ['name' => $addOn['name']],
                $addOn
            );
        }
    }
}