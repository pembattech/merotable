<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\MenuItem;
use App\Http\Resources\V1\AdminMenuItemResource;

class MenuController extends Controller
{
    public function store(Request $request)
    {
        // Get authenticated restaurant
        $restaurant = auth('restaurant')->user();

        // TODO: Add cache
        $limit = $restaurant->getFeatureLimit('menu_limit');

        if ($limit > 0 && $restaurant->menuItems()->count() >= $limit) {
            return response()->json([
                'success' => false,
                'message' => "Menu limit of {$limit} reached. Upgrade your plan.",
            ], 403);
        }

        // Validate request data
        $validatedData = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'is_available' => 'nullable|boolean',
        ]);

        // Attach restaurant_id securely
        $validatedData['restaurant_id'] = $restaurant->id;

        // Set default availability if not provided
        $validatedData['is_available'] = $validatedData['is_available'] ?? true;


        $exists = MenuItem::where('restaurant_id', $restaurant->id)
            ->where('name', $validatedData['name'])
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'A menu item with this name already exists.',
            ], 422);
        }

        // Create menu item
        $menuItem = MenuItem::create($validatedData);

        $menuItem->load(['restaurant', 'category']);

        activityLog(
            'menu_item_created',
            'Menu item created',
            [
                'menu_item_id' => $menuItem->id,
                'name' => $menuItem->name
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Menu item created successfully',
            'data' => new AdminMenuItemResource($menuItem),
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

        if (!empty($validatedData['name'])) {
            $exists = MenuItem::where('restaurant_id', $restaurant->id)
                ->where('name', $validatedData['name'])
                ->where('id', '!=', $menuItem->id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'A menu item with this name already exists.',
                ], 422);
            }
        }


        $menuItem->update($validatedData);

        $menuItem->load(['restaurant', 'category']);

        activityLog(
            'menu_item_updated',
            'Menu item updated',
            [
                'menu_item_id' => $menuItem->id,
                'name' => $menuItem->name,
                'changes' => $menuItem->getChanges()
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Menu item updated successfully',
            'data' => new AdminMenuItemResource($menuItem),
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

        activityLog(
            'menu_item_updated',
            'Menu item updated',
            [
                'menu_item_id' => $menuItem->id,
                'name' => $menuItem->name,
                'changes' => $menuItem->getChanges()
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Menu availability updated successfully',
            'data' => new AdminMenuItemResource($menuItem),
        ]);
    }

    public function show(MenuItem $menuItem)
    {
        $restaurant = auth('restaurant')->user();

        // 🔒 Ownership check: restaurant can only fetch its own items
        if ($menuItem->restaurant_id !== $restaurant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        $menuItem->load(['restaurant', 'category']);

        return response()->json([
            'success' => true,
            'data' => new AdminMenuItemResource($menuItem),
        ]);
    }






}
