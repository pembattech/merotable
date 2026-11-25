<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('menu_item_id')
                ->constrained()
                ->restrictOnDelete();

            // Selected menu size
            $table->foreignId('menu_item_size_id')
                ->nullable()
                ->constrained('menu_item_sizes')
                ->nullOnDelete();

            // Size snapshot
            $table->string('size_name', 50)->nullable();
            $table->string('size_code', 20)->nullable();

            $table->unsignedInteger('kot_number')->default(1);

            // Order item status
            $table->enum('status', [
                'pending',
                'preparing',
                'ready',
                'served',
                'cancelled',
                'paid'
            ])->default('pending');

            // Quantity and pricing
            $table->unsignedInteger('quantity')->default(1);

            $table->decimal('price', 10, 2);

            $table->decimal('subtotal', 10, 2);

            $table->text('note')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['order_id', 'status']);
            $table->index('menu_item_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
