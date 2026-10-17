<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

use App\Models\OrderItem;
use App\Models\Order;

use App\Http\Resources\V1\FinancialReportResource;

class FinancialReportController extends Controller
{

    public function index(Request $request)
    {
        $filters = $request->validate([
            'date_from' => ['required', 'date_format:Y-m-d'],
            'date_to' => [
                'required',
                'date_format:Y-m-d',
                'after_or_equal:date_from',
            ],
        ]);

        $restaurant = auth('restaurant')->user();

        abort_unless($restaurant, 401, 'Unauthenticated.');

        $from = Carbon::parse($filters['date_from'])->startOfDay();
        $to = Carbon::parse($filters['date_to'])->endOfDay();

        // Total orders for the selected date range
        $orders = Order::query()
            ->with('orderItems.menuItem')
            ->where('restaurant_id', $restaurant->id)
            ->whereBetween('created_at', [$from, $to])
            ->get();



        // Top 10 menu items for the selected date range
        $topItems = OrderItem::query()
            ->select(
                'menu_items.id',
                'menu_items.name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw(
                    'SUM(order_items.quantity * order_items.price) as revenue'
                )
            )
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
            ->whereHas('order.restaurant', function ($query) use ($restaurant) {
                $query->where('restaurants.id', $restaurant->id);
            })
            ->whereBetween('orders.created_at', [$from, $to])
            ->groupBy('menu_items.id', 'menu_items.name')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();


            // Get the database-specific hour expression for SQLite, MySQL, and PostgreSQL
            $driver = DB::connection()->getDriverName();
            $hourExpression = match ($driver) {
                'sqlite' => "CAST(strftime('%H', orders.created_at) AS INTEGER)",
                'mysql' => 'HOUR(orders.created_at)',
                'pgsql' => 'EXTRACT(HOUR FROM orders.created_at)',
                default => throw new \RuntimeException(
                    "Unsupported database driver: {$driver}"
                ),
            };

            // Peak-hour orders across four timelines
            $peakHours = Order::query()
                ->where('restaurant_id', $restaurant->id)
                ->whereBetween('orders.created_at', [$from, $to])
                ->selectRaw("
            CASE
                WHEN {$hourExpression} BETWEEN 6 AND 11 THEN 'Morning'
                WHEN {$hourExpression} BETWEEN 12 AND 16 THEN 'Afternoon'
                WHEN {$hourExpression} BETWEEN 17 AND 20 THEN 'Evening'
                ELSE 'Night'
            END AS timeline
        ")
                ->selectRaw('COUNT(*) AS total_orders')
                ->groupBy('timeline')
                ->get()
                ->keyBy('timeline');

            // Ensure all four timelines are returned, even if there are no orders
            $peakHours = collect([
                'Morning',
                'Afternoon',
                'Evening',
                'Night',
            ])->map(function ($timeline) use ($peakHours) {
                return [
                    'timeline' => $timeline,
                    'total_orders' => $peakHours->get($timeline)->total_orders ?? 0,
                ];
            });

            // table perfomance
            $tablePerformance = Order::query()
                ->join('tables', 'orders.table_id', '=', 'tables.id')
                ->leftJoin('invoices', 'invoices.order_id', '=', 'orders.id')
                ->where('orders.restaurant_id', $restaurant->id)
                ->whereBetween('orders.created_at', [$from, $to])
                ->select(
                    'tables.id as table_id',
                    'tables.table_number'
                )
                ->selectRaw('COUNT(DISTINCT orders.id) as total_orders')
                ->selectRaw('COALESCE(SUM(invoices.total_amount), 0) as total_revenue')
                ->groupBy('tables.id', 'tables.table_number')
                ->orderByDesc('total_revenue')
                ->get();

        return new FinancialReportResource([
            'filters' => [
                'date_from' => $filters['date_from'],
                'date_to' => $filters['date_to'],
            ],
            'orders' => $orders,
            'total_orders' => $orders->count(),
            'top_items' => $topItems,
            'peak_hours' => $peakHours,
            'table_performance' => $tablePerformance,
        ]);
    }

}
