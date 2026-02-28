<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('restaurant_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('plan_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->timestamp('starts_at')->useCurrent();
            $table->timestamp('expires_at')->nullable();

            $table->enum('status', [
                'trial',
                'active',
                'expired'
            ])->default('trial');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};