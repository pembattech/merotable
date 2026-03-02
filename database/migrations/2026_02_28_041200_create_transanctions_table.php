<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('restaurant_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('plan_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();

            $table->decimal('amount', 10, 2);

            $table->enum('type', [
                'subscription',
                'add_on'
            ]);

            $table->string('reference_id')->unique();
            $table->string('payment_method')->nullable();

            $table->enum('billing_cycle', ['semiannually', 'annually']);

            $table->enum('status', [
                'pending',
                'completed',
                'failed'
            ])->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};