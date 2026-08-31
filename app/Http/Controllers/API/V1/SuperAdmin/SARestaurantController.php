<?php

namespace App\Http\Controllers\API\V1\SuperAdmin;

use App\Models\Restaurant;
use App\Models\RestaurantDocuments;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Transaction;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class SARestaurantController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Restaurant::query()
            ->withCount([
                'orders as orders_lifetime_count',
                'orders as orders_today_count' => fn($q) => $q->whereDate('created_at', today()),
            ]);

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('owner_name', 'like', "%{$search}%");
            });
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $restaurants = $query->latest()->paginate(15);

        return response()->json($restaurants);
    }

    public function show(Restaurant $restaurant): JsonResponse
    {
        $restaurant->load([
            'staff:id,name,email,restaurant_id,role,created_at', // adjust columns to your real users table
            'currentSubscription.plan',
        ])->loadCount([
                    'orders as orders_lifetime_count',
                    'orders as orders_today_count' => fn($q) => $q->whereDate('created_at', today()),
                ]);

        return response()->json($restaurant);
    }

    public function update(Request $request, Restaurant $restaurant): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email'],
            'contact_number' => ['sometimes', 'string', 'max:20'],
            'address' => ['sometimes', 'string'],
            'description' => ['sometimes', 'string'],
            'status' => ['sometimes', 'in:active,inactive,suspended'],
        ]);

        $restaurant->update($validated);

        return response()->json(['message' => 'Restaurant updated', 'restaurant' => $restaurant]);
    }

    public function destroy(Restaurant $restaurant): JsonResponse
    {
        $restaurant->delete();

        return response()->json(['message' => 'Restaurant deleted']);
    }

    // public function toggleStatus(Restaurant $restaurant): JsonResponse
    // {
    //     $restaurant->status = $restaurant->status === 'active' ? 'blocked' : 'active';
    //     $restaurant->save();

    //     return response()->json(['message' => 'Status updated', 'status' => $restaurant->status]);
    // }

    /**
     * List all pending restaurants
     */
    public function pending()
    {
        return response()->json([
            'data' => Restaurant::where('status', 'pending')->latest()->get()
        ]);
    }

    /**
     * Approve a restaurant
     */
    public function approve($slug)
    {

        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();

        // Prevent double approval
        if ($restaurant->status === 'active') {
            return response()->json(['message' => 'Restaurant already approved.'], 400);
        }





        // $documents = RestaurantDocuments::where('restaurant_id', $restaurant->id);

        // $totalDocuments = $documents->count();

        // $approvedDocuments = RestaurantDocuments::where('restaurant_id', $restaurant->id)
        //     ->where('status', 'approved')
        //     ->count();

        // // Minimum required documents check
        // if ($totalDocuments < 2) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Insufficient documents uploaded.',
        //         'uploaded' => $totalDocuments
        //     ], 422);
        // }

        // // All documents must be approved
        // if ($totalDocuments !== $approvedDocuments) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Some documents are still pending approval.',
        //         'approved' => $approvedDocuments,
        //         'total' => $totalDocuments
        //     ], 422);
        // }


        // TODO: Understand transactions more deeply
        DB::transaction(function () use ($restaurant) {

            $restaurant->update(attributes: [
                'status' => 'active'
            ]);

            $demoPlan = Plan::where('name', 'Demo')->first();

            Subscription::updateOrCreate(
                ['restaurant_id' => $restaurant->id],
                [
                    'plan_id' => $demoPlan->id,
                    'starts_at' => now(),
                    'expires_at' => Carbon::now()->addDays(7),
                    'status' => 'trial',
                ]
            );

            $restaurant->setting()->create([
                'tax_percentage' => 0.00,
                'service_charge_percentage' => 0.00,
                'tax_enabled' => true,
                'service_charge_enabled' => true,
                'delivery_charge' => 0.00,
                'currency' => 'NPR'
            ]);


            /**
             * Create default tables (T1–T15)
             */
            for ($i = 1; $i <= 15; $i++) {
                $restaurant->tables()->create([
                    'table_number' => 'T' . $i,
                ]);
            }

            // Auto-create default menu categories for the restaurant after approval.
            // This reads category names from config/default_categories.php,
            // converts them into database-ready arrays,
            // and saves them using the restaurant–category relationship
            // so each category is automatically linked to this restaurant.
            $categories = collect(config('default_categories'))->map(fn($name) => [
                'name' => $name
            ])->toArray();

            $restaurantCategories = $restaurant->categories()->createMany($categories);


            // Seed sample menu items
            $sampleItems = config('sample_menu_items');

            foreach ($restaurantCategories as $category) {

                if (!isset($sampleItems[$category->name])) {
                    continue;
                }

                foreach ($sampleItems[$category->name] as $item) {
                    $restaurant->menuItems()->create([
                        'category_id' => $category->id,
                        'name' => $item['name'],
                        'price' => $item['price'],
                        'is_available' => true,
                    ]);
                }
            }


            // TODO
            /**
             * (Optional)
             * Generate QR codes here
             * Dispatch events / notifications
             */
        });

        return response()->json([
            'message' => 'Restaurant approved successfully'
        ]);

    }

    /**
     * Reject a restaurant
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:255'
        ]);

        $restaurant = Restaurant::findOrFail($id);

        if ($restaurant->status === 'approved') {
            return response()->json([
                'message' => 'Approved restaurant cannot be rejected'
            ], 400);
        }

        $restaurant->update([
            'status' => 'rejected',
            'rejection_reason' => $request->reason,
            'approved_at' => null,
        ]);

        return response()->json([
            'message' => 'Restaurant rejected successfully'
        ]);
    }

    public function approveDocuments($slug)
    {
        // TODO: Create a individual document approval function
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();

        DB::transaction(function () use ($restaurant) {
            $restaurant->documents()->update([
                'status' => 'approved',
                'verified_at' => now(),
            ]);
        });

        return response()->json([
            'message' => 'All documents approved successfully'
        ]);
    }

    public function getPendingTranscation(Request $request)
    {
        return response()->json([
            'data' => Transaction::where('status', 'pending')->latest()->get()
        ]);
    }

    public function approveTransaction($slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();

        // Get latest pending transaction
        $transaction = $restaurant->transactions()
            ->where('status', 'pending')
            ->latest()
            ->first();

        if (!$transaction) {
            return response()->json([
                'message' => 'No pending transaction found.'
            ], 404);
        }

        // Prevent double approval of same transaction
        if ($transaction->status === 'completed') {
            return response()->json([
                'message' => 'Transaction already approved.'
            ], 400);
        }

        DB::transaction(function () use ($restaurant, $transaction) {

            $plan = $transaction->plan;

            $startsAt = now();

            // Set expiry date based on transaction's billing_cycle
            $expiresAt = match ($transaction->billing_cycle) {
                'semiannually' => $startsAt->copy()->addMonths(6),
                'annually' => $startsAt->copy()->addYear()
            };

            // Expire old active subscription (if any)
            Subscription::where('restaurant_id', $restaurant->id)
                ->where('status', 'active')
                ->update([
                    'status' => 'expired',
                    'billing_cycle' => $transaction->billing_cycle
                ]);

            // Create new subscription
            Subscription::create([
                'restaurant_id' => $restaurant->id,
                'plan_id' => $plan->id,
                'starts_at' => $startsAt,
                'expires_at' => $expiresAt,
                'status' => 'active',
                'billing_cycle' => $transaction->billing_cycle,
            ]);

            // Mark transaction completed
            $transaction->update(['status' => 'completed']);

            // Activate restaurant
            $restaurant->update(['status' => 'active']);
        });

        // TODO: send message to the restaurant owner.

        return response()->json([
            'message' => 'Transaction approved and subscription activated successfully.'
        ]);
    }
}