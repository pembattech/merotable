<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Plan;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function store(Request $request)
    {
        $restaurant = auth('restaurant')->user();

        $request->validate([
            'plan_name' => 'required|exists:plans,name',
            'billing_cycle' => 'required|in:semiannually,annually',
            'amount' => 'required|numeric|min:1',
            'type' => 'required|in:subscription,add_on',
            'reference_id' => 'required|string|max:100',
            'payment_method' => 'nullable|string|max:50',

        ]);

        $plan_id = Plan::where('name', $request->plan_name)->value('id');

        $transaction = Transaction::create([
            'restaurant_id' => $restaurant->id,
            'plan_id' => $plan_id,
            'amount' => $request->amount,
            'type' => $request->type,
            'reference_id' => $request->reference_id,
            'payment_method' => $request->payment_method,
            'billing_cycle' => $request->billing_cycle,
        ]);

        activityLog(
            'subscription_transaction',
            'Subscription transaction completed',
            [
                'restaurant_id' => $transaction->restaurant_id,
                'transaction_id' => $transaction->id,
                'amount' => $transaction->amount,
                'payment_method' => $transaction->payment_method
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Transaction created successfully',
            'data' => $transaction
        ], 201);
    }


}
