<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\V1\SubscriptionResource;

class SubscriptionController extends Controller
{
    public function lastExpiredSubscription()
    {
        $restaurant = auth('restaurant')->user();

        // Eager load the latest expired subscription with its plan
        $restaurant->load(['latestExpiredSubscription.plan']);

        $expiredSubscription = $restaurant->latestExpiredSubscription;

        return response()->json([
            'success' => true,
            'data' => [
                'subscription' => $expiredSubscription
                    ? new SubscriptionResource($expiredSubscription)
                    : null,
            ],
        ]);
    }
}