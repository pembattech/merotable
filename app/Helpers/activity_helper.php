<?php

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

function activityLog($action, $description = null, $properties = [], $request = null)
{
    $restaurant_id = null;
    $user_id = null;

    if (auth('staff')->check()) {
        $user = auth('staff')->user();
        $user_id = $user->id;
        $restaurant_id = $user->restaurant_id;
    }

    if (auth('restaurant')->check()) {
        $restaurant = auth('restaurant')->user();
        $restaurant_id = $restaurant->id;
    }


    ActivityLog::create([
        'restaurant_id' => $restaurant_id,
        'user_id' => $user_id,
        'action' => $action,
        'description' => $description,
        'properties' => $properties,
        'ip_address' => request()->ip(),
        'user_agent' => request()->userAgent(),
    ]);
}