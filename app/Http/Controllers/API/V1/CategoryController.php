<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Category;
use App\Http\Resources\V1\AdminCategoryResource;


class CategoryController extends Controller
{
    public function store(Request $request)
    {
        // Get authenticated restaurant
        $restaurant = auth('restaurant')->user();

        // Validate request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255'
        ]);

        // Attach restaurant_id securely
        $validatedData['restaurant_id'] = $restaurant->id;

        $category = Category::create($validatedData);

        $category->load(['restaurant']);

        activityLog(
            'category_created',
            'New category added',
            [
                'restaurant_id' => auth()->guard('restaurant')->id(),
                'category_id' => $category->id,
                'name' => $category->name
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully',
            'data' => new AdminCategoryResource($category),
        ], 201);
    }

    public function update(Request $request, Category $category)
    {
        $restaurant = auth('restaurant')->user();

        // 🔒 Ownership check
        if ($category->restaurant_id !== $restaurant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        $validatedData = $request->validate([
            'category_id' => 'nullable|exists:restaurant,id',
            'name' => 'sometimes|string|max:255',
        ]);

        $category->update($validatedData);

        $category->load(['restaurant']);

        activityLog(
            'category_updated',
            'Category updated',
            [
                'restaurant_id' => auth()->guard('restaurant')->id(),
                'category_id' => $category->id,
                'changes' => $request->all()
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully',
            'data' => new AdminCategoryResource($category),
        ]);
    }


    public function index(Request $request)
    {
        $restaurant = auth('restaurant')->user();

        $categories = Category::where('restaurant_id', $restaurant->id)
            ->with(['restaurant'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => AdminCategoryResource::collection($categories),
        ]);
    }

}
