<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function store(Request $request)
    {
        $restaurant = auth('restaurant')->user();

        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'amount' => 'required|numeric|min:1',
            'type' => 'required|in:subscription,add_on',
            'reference_id' => 'required|string|max:100',
            'payment_method' => 'nullable|string|max:50',

        ]);

        $transaction = Transaction::create([
            'restaurant_id' => $restaurant->id,
            'plan_id' => $request->plan_id,
            'amount' => $request->amount,
            'type' => $request->type,
            'reference_id' => $request->reference_id,
            'payment_method' => $request->payment_method
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Transaction created successfully',
            'data' => $transaction
        ], 201);
    }


}
