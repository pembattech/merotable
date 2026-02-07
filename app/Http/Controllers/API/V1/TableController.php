<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Resources\V1\TableResource;

class TableController extends Controller
{
    public function fetchTables(Request $request)
    {
        $restaurant = auth('restaurant')->user();

        $tables = $restaurant->tables;
        $occupiedTablesCount = $tables->where('status', 'occupied')->count();
        $availableTablesCount = $tables->where('status', 'available')->count();
        $reservedTablesCount = $tables->where('status', 'reserved')->count();

        return response()->json([
            'success' => true,
            'message' => 'Tables fetched successfully',
            'data' => [
                'tables' => TableResource::collection($tables),
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
                    $q->whereIn('status', ['completed', 'paid']);
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
