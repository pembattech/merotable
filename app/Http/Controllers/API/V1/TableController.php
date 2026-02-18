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
        $restaurant = auth('restaurant')->user();

        // Eager load today's orders (completed + open for real-time tracking)
        // $tables = $restaurant->tables()
        //     // ->with([
        //     //     'orders' => function ($query) {
        //     //         $query->whereDate('created_at', Carbon::today());
        //     //             // ->whereIn('status', ['open', 'completed']);
        //     //     }
        //     // ])
        //     ->get();

        $tables = $restaurant->tables()
            ->with([
                'orders' => function ($query) {
                    $query->whereDate('created_at', Carbon::today());
                }
            ])
            ->orderBy('id', 'asc')
            ->get();
        // return response()->json([
        //     'tables' => $tables,
        //     'order' => $orders,
        // ]);

        // Map tables with today total amount
        $tablesData = $tables->map(function ($table) {
            $todayTotalAmount = $table->orders->sum('total_amount');

            return [
                'id' => $table->id,
                'table_number' => $table->table_number,
                'status' => $table->status,
                'today_total_amount' => $todayTotalAmount,
            ];
        });

        // Counts
        $occupiedTablesCount = $tables->where('status', 'occupied')->count();
        $availableTablesCount = $tables->where('status', 'available')->count();
        $reservedTablesCount = $tables->where('status', 'reserved')->count();

        return response()->json([
            'success' => true,
            'message' => 'Tables fetched successfully',
            'data' => [
                'tables' => $tablesData,
                'occupied_tables_count' => $occupiedTablesCount,
                'available_tables_count' => $availableTablesCount,
                'reserved_tables_count' => $reservedTablesCount,
            ],
        ], 200);
    }

    public function fetchTableDetails(Request $request, $tableId)
    {
        $restaurant = auth('restaurant')->user();

        $table = $restaurant->tables()
            ->where('id', $tableId)
            ->with([
                'orders' => function ($q) {
                    $q->whereIn('status', ['open', 'completed', 'paid'])
                    ->whereDate('created_at', Carbon::today())
                        ->with([
                            'orderItems.menuItem:id,name',
                            'activities' => function ($q) {  // activities relation
                                $q->select('id', 'order_id', 'staff_id', 'action', 'meta', 'created_at')
                                    ->with('staff:id,name,role');     // nested staff name
                            }
                        ]);
                }
            ])
            ->first();

        if (!$table) {
            return response()->json([
                'success' => false,
                'message' => 'Table not found',
            ], 404);
        }

        $totalEarning = $table->orders->sum('total_amount');

        return response()->json([
            'success' => true,
            'message' => 'Table details fetched successfully',
            'data' => [
                'table' => new TableResource($table),
                'total_earning' => $totalEarning,
            ],
        ]);
    }

}
