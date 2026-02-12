<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Restaurant;
use App\Models\Category;

use App\Http\Resources\V1\PublicMenuItemResource;
use App\Http\Resources\V1\PublicCategoryResource;

class StaffController extends Controller
{
    // public function getStaffMenu(Restaurant $restaurant)
    // {
    //     $menuItems = $restaurant->menuItems()
    //         ->where('is_available', true)
    //         ->with('category:id,name')
    //         ->orderBy('name')
    //         ->get();

    //     return response()->json([
    //         'success' => true,
    //         'count' => $menuItems->count(),
    //         'data' => PublicMenuItemResource::collection($menuItems),
    //     ]);
    // }

    public function getStaffMenu(Request $request, Restaurant $restaurant)
    {
        $menu = $restaurant->menuItems()
            ->where('is_available', true)
            ->when($request->category_id, function ($q) use ($request) {
                $q->where('category_id', $request->category_id);
            })
            ->with('category:id,name')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => PublicMenuItemResource::collection($menu)
        ]);
    }


    public function fetchPublicCategories(Request $request)
    {
        $staff = auth('staff')->user();

        $restaurant = $staff->restaurant;

        $categories = Category::whereHas('menuItems', function ($q) use ($restaurant) {
            $q->where('restaurant_id', $restaurant->id)
                ->where('is_available', true);
        })
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'count' => $categories->count(),

            'data' => PublicCategoryResource::collection($categories)
        ]);
    }

    public function fetchTables()
    {
        $staff = auth('staff')->user();
        $restaurant = $staff->restaurant;

        $tables = $restaurant->tables()->select('id', 'table_number', 'status')->orderBy('id')->get();

        return response()->json([
            'success' => true,
            'data' => $tables,
        ]);
    }



}
