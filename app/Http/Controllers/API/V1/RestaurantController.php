<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class RestaurantController extends Controller
{
    // public function index()
    // {
    //     $user = Auth::user();
    //     $restaurants = $user->isadmin 
    //         ? Restaurant::latest()->get() 
    //         : Restaurant::where('user_id', $user->id)->get();

    //     return response()->json(['success' => true, 'data' => $restaurants]);
    // }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'phone' => 'required|string|max:20',
    //         'address' => 'required|string',
    //     ]);

    //     $restaurant = Restaurant::create([
    //         'user_id' => Auth::id(),
    //         'name' => $request->name,
    //         'phone' => $request->phone,
    //         'address' => $request->address,
    //         'is_approved' => false,
    //         'is_active' => true,
    //     ]);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Restaurant registered. Waiting for admin approval.',
    //         'data' => $restaurant
    //     ], 201);
    // }

    // public function show(Restaurant $restaurant)
    // {
    //     $this->authorizeAccess($restaurant);
    //     return response()->json(['success' => true, 'data' => $restaurant]);
    // }

    // public function update(Request $request, Restaurant $restaurant)
    // {
    //     $this->authorizeAccess($restaurant);

    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'phone' => 'required|string|max:20',
    //         'address' => 'required|string',
    //     ]);

    //     $restaurant->update($request->only('name', 'phone', 'address'));

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Restaurant updated successfully',
    //         'data' => $restaurant
    //     ]);
    // }

    // public function destroy(Restaurant $restaurant)
    // {
    //     $this->authorizeAccess($restaurant);
    //     $restaurant->delete();

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Restaurant removed successfully'
    //     ]);
    // }

    // // Access control: restaurant can only manage own restaurant
    // private function authorizeAccess(Restaurant $restaurant)
    // {
    //     $user = Auth::user();
    //     if (!$user->isadmin && $restaurant->user_id !== $user->id) {
    //         abort(403, 'Unauthorized.');
    //     }
    // }


    public function getMenuItems(Restaurant $restaurant)
    {
        $menuItems = $restaurant->menuItems()->get();

        return response()->json([
            'success' => true,
            'data' => $menuItems
        ]);
    }
}
