<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\AdminMenuItemResource;
use App\Http\Resources\V1\PublicMenuItemResource;
use App\Http\Resources\V1\PublicStaffResource;
use App\Models\Restaurant;
use App\Models\User;
use App\Models\OrderActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;



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
        $menuItems = $restaurant->menuItems()->with('restaurant','category')->get();

        return response()->json([
            'success' => true,
            'category_count' => $restaurant->categories()->count(),
            'total_item_count' => $menuItems->count(),
            'active_item_count' => $menuItems->where('is_available', 1)->count(),
            'menu' => AdminMenuItemResource::collection($menuItems)
        ]);
    }

    public function createStaff(Request $request)
    {
        $restaurant = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'in:staff,waiter,kitchen,cashier,staff,manager',
            'phone' => 'required|string|max:20',
        ]);

        $staff = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'restaurant_id' => $restaurant->id,
            'role' => $request->role ?? 'staff',
            'phone' => $request->phone,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Staff created successfully',
            'data' => $staff,
        ], 201);
    }


    public function loginStaff(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $staff = User::with('restaurant')->where('email', $request->email)->first();

        if (!$staff || !Hash::check($request->password, $staff->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }

        if (!$staff->is_active) {
            return response()->json([
                'status' => 'error',
                'message' => 'Staff not active.'
            ], 403);
        }

        $token = $staff->createToken('staff-token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'staff' => new PublicStaffResource($staff),
            'token' => $token,
        ]);
    }

    public function fetchStaff()
    {
        $restaurantId = Auth::user()->id;
        $staffMembers = User::where('restaurant_id', $restaurantId)->get();

        return response()->json([
            'success' => true,
            'data' => $staffMembers
        ]);
    }

    public function restaurantActivities(Request $request)
    {
        $restaurantId = Auth::user()->id;

        return OrderActivity::with('staff:id,name')
            ->whereHas(
                'order',
                fn($q) =>
                $q->where('restaurant_id', $restaurantId)
            )
            ->latest()
            ->paginate(50);
    }



}
