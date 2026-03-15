<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\Restaurant;

use App\Http\Resources\V1\RestaurantResource;
use App\Http\Resources\V1\PublicStaffResource;

class AuthController extends Controller
{
    /* =====================================================
     | RESTAURANT REGISTRATION
     ===================================================== */
    public function registerRestaurant(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'owner_name' => 'required|string|max:150',
            'email' => 'required|email|unique:restaurants,email',
            'password' => 'required|min:6|confirmed',
            'contact_number' => 'required|string|max:20',
        ]);

        $restaurant = Restaurant::create([
            'name' => $request->name,
            'owner_name' => $request->owner_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'contact_number' => $request->contact_number,
        ]);

        activityLog(
            'restaurant_registered',
            'New restaurant registered',
            [
                'restaurant_id' => $restaurant->id,
                'email' => $restaurant->email
            ]
        );
        return response()->json([
            'status' => 'success',
            'message' => 'Restaurant registered successfully. Waiting for admin verification.',
            'restaurant' => $restaurant
        ], 201);
    }

    /* =====================================================
     | RESTAURANT LOGIN
     ===================================================== */
    public function loginRestaurant(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $restaurant = Restaurant::where('email', $request->email)->first();

        if (!$restaurant || !Hash::check($request->password, $restaurant->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }

        $restaurant->tokens()->delete();

        $token = $restaurant->createToken('restaurant-token')->plainTextToken;

        activityLog(
            'restaurant_login',
            'Restaurant logged in',
            [
                'restaurant_id' => $restaurant->id,
                'ip' => request()->ip()
            ]
        );

        return (new RestaurantResource($restaurant))
            ->additional([
                'status' => 'success',
                'token' => $token,
            ])
            ->toResponse(request());
    }

    /* =====================================================
     | USER / STAFF REGISTRATION (ADMIN ONLY)
     ===================================================== */
    public function registerUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|in:waiter,kitchen,cashier',
            'restaurant_id' => 'nullable|exists:restaurants,id',
            'phone' => 'nullable|string|max:20|unique:users,phone',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'restaurant_id' => $request->restaurant_id,
            'phone' => $request->phone,
        ]);

        return response()->json([
            'status' => 'success',
            'user' => $user
        ], 201);
    }

    /* =====================================================
     | USER / STAFF LOGIN
     ===================================================== */
    public function loginUser(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::with('restaurant')->where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }

        // Block staff if restaurant is inactive
        if ($user->restaurant_id) {
            if ($user->restaurant->status !== 'active') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Restaurant is not active.'
                ], 403);
            }
        }

        $token = $user->createToken($user->role . '-token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'token' => $token,
            'data' => new PublicStaffResource($user),
        ]);
    }

    /* =====================================================
     | LOGOUT (COMMON)
     ===================================================== */
    public function logout(Request $request)
    {

        // Delete current access token
        $request->user()->currentAccessToken()->delete();

        $user_id = null;
        $user_type = null;
        $restaurant_id = null;

        // Check restaurant guard
        if (Auth::guard('restaurant')->check()) {
            $user_type = 'restaurant';
            $user_id = Auth::guard('restaurant')->id();
            $restaurant_id = Auth::guard('restaurant')->id();
        }

        // Check staff guard
        elseif (Auth::guard('staff')->check()) {
            $user_type = 'staff';
            $user_id = Auth::guard('staff')->id();
            $restaurant_id = Auth::guard('staff')->user()->restaurant_id;
        }

        // Log logout activity
        activityLog(
            'logout',
            'User logged out',
            [
                'user_type' => $user_type,
                'user_id' => $user_id,
                'restaurant_id' => $restaurant_id,
                'ip_address' => $request->ip()
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }
}
