<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use App\Models\Restaurant;
use App\Models\RestaurantDocuments;

class AdminController extends Controller
{
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
    public function approve($id)
    {
        // TODO: Understand transactions more deeply
        DB::transaction(function () use ($id) {

            $restaurant = Restaurant::findOrFail($id);

            // Prevent double approval
            if ($restaurant->status === 'active') {
                return response()->json(['message' => 'Restaurant already approved.'], 400);
            }

            // Active restaurant
            $active_restaurant = $restaurant->update(attributes: [
                'status' => 'active',
                'approved_at' => Carbon::now(),
            ]);


            if ($active_restaurant) {
                RestaurantDocuments::where('restaurant_id', $restaurant->id)
                    ->update(['status' => 'approved']);
            }

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
}
