<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Http\Resources\V1\TableResource;
use App\Models\Table;
use App\Models\Restaurant;

class TableController extends Controller
{

    // app/Http/Controllers/TableController.php

    // public function store(Request $request)
    // {
    //     $restaurant = auth()->user()->restaurant;

    //     // Uses your getFeatureLimit() — returns 0 if no plan/feature
    //     $limit = $restaurant->getFeatureLimit('tables_limit');

    //     if ($limit > 0 && $restaurant->tables()->count() >= $limit) {
    //         return back()->with('error', "Table limit of {$limit} reached. Upgrade your plan.");
    //     }

    //     $request->validate([
    //         'table_number' => [
    //             'required',
    //             'string',
    //             'max:5',
    //             Rule::unique('tables')->where('restaurant_id', $restaurant->id),
    //         ],
    //         'status' => ['required', Rule::in(['available', 'occupied', 'reserved'])],
    //     ]);

    //     $restaurant->tables()->create($request->only('table_number', 'status'));

    //     return back()->with('success', 'Table added successfully.');
    // }

    // public function storeBulk(Request $request)
    // {
    //     $restaurant = auth()->user()->restaurant;
    //     $limit = $restaurant->getFeatureLimit('tables_limit');
    //     $current = $restaurant->tables()->count();
    //     $incoming = count($request->tables);

    //     if ($limit > 0 && ($current + $incoming) > $limit) {
    //         $remaining = max(0, $limit - $current);
    //         return back()->with('error', "You can only add {$remaining} more table(s). Plan limit: {$limit}.");
    //     }

    //     $request->validate([
    //         'tables' => ['required', 'array', 'min:1', 'max:100'],
    //         'tables.*.table_number' => ['required', 'string', 'max:5'],
    //         'tables.*.status' => ['required', Rule::in(['available', 'occupied', 'reserved'])],
    //     ]);

    //     $now = now();
    //     $rows = collect($request->tables)->map(fn($t) => [
    //         'restaurant_id' => $restaurant->id,
    //         'table_number' => strtoupper($t['table_number']),
    //         'status' => $t['status'],
    //         'created_at' => $now,
    //         'updated_at' => $now,
    //     ])->toArray();

    //     DB::table('tables')->insertOrIgnore($rows);

    //     return back()->with('success', 'Tables added.');
    // }

    public function tableStatus(Restaurant $restaurant, $tableId)
    {
        $staff = auth('staff')->user();


        $tables = Table::where('id', $tableId)
            // ->where('restaurant_id', $staff->restaurant_id)
            ->first();

        return response()->json([
            'success' => true,
            'tableStatus' => $tables->status,
        ]);
    }


    public function fetchTables(Request $request)
    {
        $mode = $request->get('mode', 'default');

        $restaurant = auth('restaurant')->user()
            ?? auth('staff')->user()->restaurant;

        $tables = $restaurant->tables()
            ->with([
                'orders' => function ($query) use ($mode) {

                    if ($mode === 'billing') {
                        // Only open orders
                        $query->where('status', 'open')
                            ->select('id', 'table_id', 'total_amount', 'status', 'created_at');
                    } else {
                        // Today's orders
                        $query->whereDate('created_at', today())
                            ->select('id', 'table_id', 'total_amount', 'status', 'created_at');
                    }

                }
            ])
            ->orderBy('id', 'asc')
            ->get();

        // Map tables
        $tablesData = $tables->map(function ($table) use ($mode) {

            if ($mode === 'billing') {
                // Only open order total
                $amount = optional(
                    $table->orders->first()
                )->total_amount ?? 0;

            } else {
                // Today's total
                $amount = $table->orders->sum('total_amount');
            }

            return [
                'id' => $table->id,
                'table_number' => $table->table_number,
                'status' => $table->status,
                'total_amount' => $amount,
            ];
        });

        // Counts
        $occupiedTablesCount = $tables->where('status', 'occupied')->count();
        $availableTablesCount = $tables->where('status', 'available')->count();
        $reservedTablesCount = $tables->where('status', 'reserved')->count();

        return response()->json([
            'success' => true,
            'data' => [
                'mode' => $mode,
                'tables' => $tablesData,
                'occupied_tables_count' => $occupiedTablesCount,
                'available_tables_count' => $availableTablesCount,
                'reserved_tables_count' => $reservedTablesCount,
            ],
        ]);
    }

    // Fetch table details with orders based on mode (billing = open order only, default = today's orders)
    // Includes items and optional staff activities. Optimized with eager loading.
    public function fetchTableDetails(Request $request, $slug, $tableId)
    {
        $mode = $request->get('mode', 'default');

        $auth = auth('restaurant')->user() ?? auth('staff')->user();
        $isRestaurant = isset($auth->slug);

        $restaurant = auth('restaurant')->user()
            ?? auth('staff')->user()->restaurant;

        $tableQuery = $restaurant->tables()
            ->where('id', $tableId);

        // ---- ORDERS RELATION ----
        $tableQuery->with([
            'orders' => function ($q) use ($mode, $isRestaurant) {

                if ($mode === 'billing') {
                    $q->where('status', 'open')->latest()->limit(1);
                } else {
                    $q->whereIn('status', ['open', 'completed', 'paid'])
                        ->whereDate('created_at', today());
                }

                $q->select('id', 'table_id', 'status', 'total_amount', 'created_at');

                $q->with([
                    'orderItems:id,order_id,menu_item_id,price,quantity,status',
                    'orderItems.menuItem:id,name'
                ]);

                if ($isRestaurant) {
                    $q->with([
                        'activities' => function ($q) {
                            $q->select('id', 'order_id', 'staff_id', 'action', 'meta', 'created_at')
                                ->with('staff:id,name,role');
                        }
                    ]);
                }
            }
        ]);

        $table = $tableQuery->first();

        if (!$table) {
            return response()->json([
                'success' => false,
                'message' => 'Table not found'
            ], 404);
        }

        // ---- TOTAL ----
        $totalEarning = $mode === 'billing'
            ? optional($table->orders->first())->total_amount
            : $table->orders->sum('total_amount');

        return response()->json([
            'success' => true,
            'data' => [
                'table' => new TableResource($table),
                'total' => $totalEarning,
                'mode' => $mode
            ]
        ]);
    }

    public function tableUpdateStatus(Request $request, $slug, $tableId)
    {
        $staff = auth('staff')->user();

        $table = Table::where('id', $tableId)
            ->where('restaurant_id', $staff->restaurant_id)
            ->first();

        if (!$table) {
            return response()->json([
                'success' => false,
                'message' => 'Table not found',
            ], 404);
        }

        // Validate input
        $validated = $request->validate([
            'status' => 'required|string|in:available,occupied,reserved',
        ]);

        // Update table
        $table->status = $validated['status'];
        $table->save();

        return response()->json([
            'success' => true,
            'tableStatus' => $table->status,
        ]);
    }



}
