<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\RestaurantSetting;
use App\Http\Resources\V1\RestaurantSettingResource;

class RestaurantSettingController extends Controller
{
    // Get current restaurant settings
    public function show(Request $request)
    {
        $restaurant = auth('restaurant')->user();

        $settings = $restaurant->setting;

        if (!$settings) {
            return response()->json([
                'success' => false,
                'message' => 'Restaurant settings not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new RestaurantSettingResource($settings),
        ]);
    }

    public function update(Request $request)
    {
        $restaurant = auth('restaurant')->user();

        $settings = $restaurant->setting;

        // If settings not found, return 404
        if (!$settings) {
            return response()->json([
                'success' => false,
                'message' => 'Restaurant settings not found.'
            ], 404);
        }

        // Validate incoming data
        $validated = $request->validate([
            'tax_percentage' => 'nullable|numeric|min:0|max:100',
            'service_charge_percentage' => 'nullable|numeric|min:0|max:100',
            'tax_enabled' => 'nullable|boolean',
            'service_charge_enabled' => 'nullable|boolean',
            'delivery_charge' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:10',
        ]);

        // Update only validated fields
        $settings->update($validated);

        activityLog(
            'restaurant_settings_updated',
            'Restaurant updated settings',
            [
                'restaurant_id' => auth()->guard('restaurant')->id(),
                'changes' => $request->all()
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Restaurant settings updated successfully.',
            'data' => new RestaurantSettingResource($settings)
        ]);
    }


    public function deactivate(Request $request)
    {
        $restaurant = auth('restaurant')->user();

        $oldStatus = $restaurant->status;

        $restaurant->status = 'inactive';
        $restaurant->save();

        activityLog(
            'restaurant_deactivated',
            'Restaurant account deactivated',
            [
                'restaurant_id' => $restaurant->id,
                'old_status' => $oldStatus,
                'new_status' => $restaurant->status,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Restaurant deactivated successfully',
            'data' => $restaurant
        ]);
    }

}
