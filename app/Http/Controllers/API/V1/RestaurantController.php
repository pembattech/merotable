<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\AdminMenuItemResource;
use App\Http\Resources\V1\PublicMenuItemResource;
use App\Http\Resources\V1\PublicStaffResource;
use App\Http\Resources\V1\RestaurantDashboardResource;
use App\Http\Resources\V1\RestaurantProfileResource;
use App\Http\Resources\V1\RestaurantResource;
use App\Models\Restaurant;
use App\Models\User;
use App\Models\OrderActivity;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;




class RestaurantController extends Controller
{

    public function index()
    {
        $restaurant = auth('restaurant')->user();
        $restaurant->load('currentSubscription.plan');

        $todayStart = Carbon::today();
        $todayEnd = Carbon::today()->endOfDay();

        $yesterdayStart = Carbon::yesterday();
        $yesterdayEnd = Carbon::yesterday()->endOfDay();

        $stats = $restaurant->orders()
            ->selectRaw("
        SUM(CASE WHEN created_at BETWEEN ? AND ? AND status = 'completed' THEN total_amount ELSE 0 END) as today_revenue,
        SUM(CASE WHEN created_at BETWEEN ? AND ? AND status = 'completed' THEN total_amount ELSE 0 END) as yesterday_revenue,
        SUM(CASE WHEN status = 'open' THEN 1 ELSE 0 END) as active_orders
    ", [
                $todayStart,
                $todayEnd,
                $yesterdayStart,
                $yesterdayEnd
            ])
            ->first();

        $occupiedTables = $restaurant->tables()
            ->where('status', 'occupied')
            ->count();

        // Revenue change calculation
        $changePercent = 0;
        if ($stats->yesterday_revenue > 0) {
            $changePercent = (($stats->today_revenue - $stats->yesterday_revenue) / $stats->yesterday_revenue) * 100;
        } elseif ($stats->today_revenue > 0) {
            $changePercent = 100;
        }

        // Top 10 menu items today
        $topItems = OrderItem::query()
            ->select(
                'menu_items.id',
                'menu_items.name',
                DB::raw('SUM(order_items.quantity) as total_orders'),
                DB::raw('SUM(order_items.quantity * order_items.price) as revenue')
            )
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
            ->where('orders.restaurant_id', $restaurant->id)
            ->whereDate('orders.created_at', $todayStart)
            ->groupBy('menu_items.id', 'menu_items.name')
            ->orderByDesc('total_orders')
            ->limit(10)
            ->get();

        // Attach to restaurant
        $restaurant->today_revenue = $stats->today_revenue;
        $restaurant->yesterday_revenue = $stats->yesterday_revenue;
        $restaurant->active_orders_count = $stats->active_orders;
        $restaurant->occupied_tables_count = $occupiedTables;
        $restaurant->revenue_change_percent = round($changePercent, 2);
        $restaurant->top_items_today = $topItems;


        return response()->json([
            'success' => true,
            'data' => new RestaurantDashboardResource($restaurant),
        ]);
    }

    public function show(Request $request)
    {
        $restaurant = auth('restaurant')
            ->user()
            ->load(['setting', 'documents']);

        return new RestaurantProfileResource($restaurant);
    }

    public function update(Request $request)
    {
        $restaurant = auth('restaurant')->user();


        // Validation
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255|unique:restaurants,slug,' . $restaurant->id,
            'email' => 'nullable|email|max:255|unique:restaurants,email,' . $restaurant->id,
            'contact_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // dd($request->all());

        // return response()->json([
        //     'success' => true,
        //     'request' => $validated,
        // ]);

        // Update basic info
        $data = $request->only('name', 'slug', 'email', 'contact_number', 'address', 'description');

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($restaurant->logo && \Storage::disk('public')->exists($restaurant->logo)) {
                \Storage::disk('public')->delete($restaurant->logo);
            }

            $path = $request->file('logo')->store('logos', 'public');

            $data['logo'] = $path;
        }

        $restaurant->update($data);



        return response()->json([
            'success' => true,
            'message' => 'Restaurant information updated successfully.',
            'data' => new RestaurantProfileResource($restaurant),
        ]);
    }


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
        $menuItems = $restaurant->menuItems()->with('restaurant', 'category')->get();

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
        $restaurant = Auth::guard('restaurant')->user();

        // TODO: Add cache
        $limit = $restaurant->getFeatureLimit('staff_limit');

        if ($limit > 0 && $restaurant->staff()->count() >= $limit) {
            return response()->json([
                'success' => false,
                'message' => "Staff limit of {$limit} reached. Upgrade your plan.",
            ], 403);
        }

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

    public function updateStaff(Request $request, $id)
    {
        $restaurant = Auth::user();

        $staff = User::where('restaurant_id', $restaurant->id)->findOrFail($id);

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $staff->id,
            'password' => 'sometimes|min:6',
            'role' => 'sometimes|in:staff,waiter,kitchen,cashier,manager',
            'phone' => 'sometimes|string|max:20',
        ]);

        if ($request->has('name')) {
            $staff->name = $request->name;
        }

        if ($request->has('email')) {
            $staff->email = $request->email;
        }

        if ($request->has('phone')) {
            $staff->phone = $request->phone;
        }

        if ($request->has('role')) {
            $staff->role = $request->role;
        }

        if ($request->filled('password')) {
            $staff->password = Hash::make($request->password);
        }

        $staff->save();

        return response()->json([
            'success' => true,
            'message' => 'Staff updated successfully',
            'data' => $staff,
        ], 200);
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
