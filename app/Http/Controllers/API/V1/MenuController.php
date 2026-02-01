<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\MenuItem;

class MenuController extends Controller
{
    public function store(Request $request)
    {
        // Get authenticated restaurant
        $restaurant = auth('restaurant')->user();

        // Validate request data
        $validatedData = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'is_available' => 'sometimes|boolean',
        ]);

        // Attach restaurant_id securely
        $validatedData['restaurant_id'] = $restaurant->id;

        // Set default availability if not provided
        $validatedData['is_available'] = $validatedData['is_available'] ?? true;



        // Create menu item
        $menuItem = MenuItem::create($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Menu item created successfully',
            'data' => $menuItem,
        ], 201);
    }

    public function update(Request $request, MenuItem $menuItem)
    {
        $restaurant = auth('restaurant')->user();

        // 🔒 Ownership check
        if ($menuItem->restaurant_id !== $restaurant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        $validatedData = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'name' => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric|min:0',
            'is_available' => 'sometimes|boolean',
        ]);

        $menuItem->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Menu item updated successfully',
            'data' => $menuItem
        ]);
    }

    public function updateAvailability(Request $request, MenuItem $menuItem)
    {
        $restaurant = auth('restaurant')->user();

        // 🔒 Ownership check
        if ($menuItem->restaurant_id !== $restaurant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        $validated = $request->validate([
            'is_available' => 'required|boolean',
        ]);

        $menuItem->update([
            'is_available' => $validated['is_available']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Menu availability updated successfully',
            'data' => $menuItem
        ]);
    }





}
