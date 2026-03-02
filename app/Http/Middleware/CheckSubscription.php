<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        $restaurant = auth('restaurant')->user();

        if (!$restaurant) {
            return response()->json([
                'message' => 'Unauthorized',
                'redirect_url' => '/auth'
            ], 401);
        }

        if ($restaurant->status === 'expired') {
            return response()->json([
                'message' => 'Subscription expired or inactive',
                'redirect_url' => '/pricing'
            ], 403);
        }

        $subscription = $restaurant->currentSubscription;

        // Calculate days left
        $daysLeft = 0;
        if ($subscription && $subscription->expires_at) {
            $daysLeft = (int) now()->diffInDays($subscription->expires_at, false);
        }

        // If no subscription or expired (days_left <= 0)
        if (!$subscription || $daysLeft <= 0) {

            // Update subscription status if exists
            if ($subscription && $subscription->status !== 'expired') {
                $subscription->status = 'expired';
                $subscription->save();
            }

            // Update restaurant status if not already expired
            if ($restaurant->status !== 'expired') {
                $restaurant->status = 'expired';
                $restaurant->save();
            }

            // Return 403 with redirect
            return response()->json([
                'message' => 'Subscription expired or inactive',
                'redirect_url' => '/pricing'
            ], 403);
        }

        // Attach days left for active subscription
        $request->merge(['subscription_days_left' => $daysLeft, 'test' => $subscription]);

        return $next($request);
    }
}