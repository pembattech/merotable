<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantDashboardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            
            'slug' => $this->slug,

            // Core restaurant info
            'name' => $this->name,
            
            // Counts of related entities for dashboard stats
            'staffCount' => $this->staff()->count(),
            'tableCount' => $this->tables()->count(),
            'menuCount' => $this->menuItems()->count(),

            // Counts
            'activeOrders' => $this->active_orders_count ?? 0,
            'occupiedTables' => $this->occupied_tables_count ?? 0,

            // Revenue
            'todayRevenue' => $this->orders()
                ->where('status', 'completed')
                ->sum('total_amount'),

            'yesterdayRevenue' => $this->orders()
                ->where('status', 'completed')
                ->whereDate('created_at', now()->subDay())
                ->sum('total_amount'),

            'revenueChangePercent' => $this->revenue_change_percent,

            // topItems
            'topItems' => $this->top_items_today,

            // // Relationships (optional, eager load in controller to avoid N+1)
            // 'categories' => PublicCategoryResource::collection(
            //     $this->whenLoaded('categories')
            // ),
            // 'menuItems' => PublicMenuItemResource::collection(
            //     $this->whenLoaded('menuItems')
            // ),

            // 'subscriptionHistory' => SubscriptionResource::collection(
            //     $this->whenLoaded('subscriptions')
            // ),

            'activeSubscription' => $this->whenLoaded('currentSubscription', function () {
                $active = $this->currentSubscription;

                return [
                    // Subscription info
                    'startsAt' => $active->starts_at,
                    'expiresAt' => $active->expires_at,
                    'status' => $active->status,
                    'daysLeft' => $active->expires_at
                        ? now()->diffInDays($active->expires_at, false)
                        : null,

                    // Nested plan info
                    'plan' => $active->plan ? new PlanResource($active->plan) : null,
                ];
            }),

            // Optional: add logo or image URL if available
            'logo' => $this->logo_url ?? null,
            'address' => $this->address ?? null,
        ];
    }
}
