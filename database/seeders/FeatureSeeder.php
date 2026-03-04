<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Feature;

class FeatureSeeder extends Seeder
{
    public function run(): void
    {
        $features = [
            ['name' => 'tables_limit', 'type' => 'limit', 'description' => 'Maximum number of tables'],
            ['name' => 'staff_limit', 'type' => 'limit', 'description' => 'Maximum number of staff'],
            ['name' => 'menu_limit', 'type' => 'limit', 'description' => 'Maximum number of Menu'],
            ['name' => 'category_limit', 'type' => 'limit', 'description' => 'Maximum number of Category'],
            ['name' => 'qr_order', 'type' => 'boolean', 'description' => 'Enable QR ordering'],
            ['name' => 'analytics', 'type' => 'boolean', 'description' => 'Access to analytics'],
            ['name' => 'export_reports', 'type' => 'boolean', 'description' => 'Download reports'],
        ];

        foreach ($features as $feature) {
            Feature::updateOrCreate(
                ['name' => $feature['name']],
                $feature
            );
        }
    }
}