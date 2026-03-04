<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use \App\Http\Resources\V1\PublicCategoryResource;
use \App\Http\Resources\V1\PublicMenuItemResource;

class RestaurantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [

            'slug' => $this->slug,

            // Core restaurant info
            'name' => $this->name,
            'email' => $this->email,
            'contact_number' => $this->contact_number,
            'status' => $this->status,

            // Computed property to quickly check if restaurant is active
            'isActive' => $this->status === 'active',

            // Timestamps in camelCase for frontend consistency
            'createdAt' => $this->created_at->toDateTimeString(),
            'updatedAt' => $this->updated_at->toDateTimeString(),

            // Optional: human-readable created time
            'createdAgo' => $this->created_at->diffForHumans(),

            // Counts of related entities for dashboard stats
            'staffCount' => $this->staff()->count(),
            'tableCount' => $this->tables()->count(),
            'menuCount' => $this->menuItems()->count(),

            // Counts
            'activeOrders' => $this->active_orders_count ?? 0,
            'occupiedTables' => $this->occupied_tables_count ?? 0,

            // Revenue
            'todayRevenue' => $this->today_revenue,

            'yesterdayRevenue' => $this->yesterday_revenue,

            'revenueChangePercent' => $this->revenue_change_percent,

            // Relationships (optional, eager load in controller to avoid N+1)
            'categories' => PublicCategoryResource::collection(
                $this->whenLoaded('categories')
            ),
            'menuItems' => PublicMenuItemResource::collection(
                $this->whenLoaded('menuItems')
            ),

            'subscriptionHistory' => SubscriptionResource::collection(
                $this->whenLoaded('subscriptions')
            ),

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
