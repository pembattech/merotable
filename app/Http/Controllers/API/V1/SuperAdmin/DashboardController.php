<?php

namespace App\Http\Controllers\API\V1\SuperAdmin;

use App\Http\Controllers\Controller;

use App\Models\Restaurant;
use App\Models\User;
use App\Models\Order;
use App\Models\Subscription;


use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // public function index()
    // {
    //     return view('super-admin.dashboard', [
    //         'restaurants' => Restaurant::count(),
    //         'users' => User::count(),
    //         'orders' => Order::count(),
    //         'subscriptions' => Subscription::count(),
    //     ]);
    // }



    public function index(): JsonResponse
    {
        return response()->json([
            'stats' => $this->getStats(),
            'recent_activity' => $this->getRecentActivity(),
            'restaurants' => Restaurant::count(),
            'users' => User::count(),
            'orders' => Order::count(),
            'subscriptions' => Subscription::count(),
        ]);
    }

    protected function getStats(): array
    {
        return [
            'total_restaurants' => $this->safeCount('restaurants'),
            'active_subscriptions' => $this->safeCount('subscriptions', ['status' => 'active']),
            'total_revenue' => $this->safeSum('transactions', 'amount', ['status' => 'success']),
            'total_plans' => $this->safeCount('plans'),
        ];
    }

    protected function getRecentActivity(): array
    {
        if (!$this->tableExists('transactions')) {
            return [];
        }

        return DB::table('transactions')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(fn($row) => [
                'id' => $row->id,
                'amount' => $row->amount ?? null,
                'status' => $row->status ?? null,
                'created_at' => $row->created_at,
            ])
            ->toArray();
    }

    /**
     * Guards against tables that don't exist yet in your schema
     * (Restaurant, Subscription, Plan, Transaction models),
     * so the dashboard doesn't 500 while you're still building those out.
     */
    protected function tableExists(string $table): bool
    {
        return \Illuminate\Support\Facades\Schema::hasTable($table);
    }

    protected function safeCount(string $table, array $where = []): int
    {
        if (!$this->tableExists($table)) {
            return 0;
        }

        $query = DB::table($table);

        foreach ($where as $col => $val) {
            $query->where($col, $val);
        }

        return $query->count();
    }

    protected function safeSum(string $table, string $column, array $where = []): float
    {
        if (!$this->tableExists($table)) {
            return 0;
        }

        $query = DB::table($table);

        foreach ($where as $col => $val) {
            $query->where($col, $val);
        }

        return (float) $query->sum($column);
    }
}