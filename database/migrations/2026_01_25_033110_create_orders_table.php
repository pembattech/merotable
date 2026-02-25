<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('restaurant_id')
                ->constrained('restaurants')
                ->cascadeOnDelete()
                ->index(); // index for faster restaurant queries

            $table->foreignId('table_id')
                ->constrained('tables')
                ->cascadeOnDelete()
                ->index(); // index for table-specific queries

            $table->enum('status', ['open', 'completed', 'cancelled'])
                ->default('open')
                ->index(); // index to filter by order status

            $table->decimal('total_amount', 10, 2)->default(0);

            $table->text('remarks')->nullable();

            $table->timestamps();

            // Optional: composite index if you often query by restaurant + status
            $table->index(['restaurant_id', 'status']);
            // Optional: composite index for table + status queries
            $table->index(['table_id', 'status']);
            // Optional: index on created_at for reports or sorting
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
