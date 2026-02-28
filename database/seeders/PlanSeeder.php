<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;
use App\Models\Feature;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $demo = Plan::updateOrCreate(
            ['name' => 'Demo'],
            ['price' => 0, 'duration' => 'annually']
        );

        // 25 per day
        $basic = Plan::updateOrCreate(
            ['name' => 'Basic'],
            ['price' => 9000, 'duration' => 'annually']
        );

        // 55.53 per day
        $premium = Plan::updateOrCreate(
            ['name' => 'Premium'],
            ['price' => 20000, 'duration' => 'annually']
        );

        // 111.1 per day
        $platinum = Plan::updateOrCreate(
            ['name' => 'Platinum'],
            ['price' => 40000, 'duration' => 'annually']
        );

        $this->attachFeatures($demo, [
            'tables_limit' => '15',
            'staff_limit' => '5',
            'qr_order' => 'false',
            'analytics' => 'false',
            'export_reports' => 'false',
        ]);

         $this->attachFeatures($basic, [
            'tables_limit' => '15',
            'staff_limit' => '5',
            'qr_order' => 'true',
            'analytics' => 'true',
            'export_reports' => 'false',
        ]);

        $this->attachFeatures($premium, [
            'tables_limit' => '50',
            'staff_limit' => '20',
            'qr_order' => 'true',
            'analytics' => 'true',
            'export_reports' => 'true',
        ]);

        $this->attachFeatures($platinum, [
            'tables_limit' => '9999',
            'staff_limit' => '9999',
            'qr_order' => 'true',
            'analytics' => 'true',
            'export_reports' => 'true',
        ]);
    }

    private function attachFeatures($plan, $features)
    {
        foreach ($features as $featureName => $value) {
            $feature = Feature::where('name', $featureName)->first();
            if ($feature) {
                $plan->features()->syncWithoutDetaching([
                    $feature->id => ['value' => $value]
                ]);
            }
        }
    }
}