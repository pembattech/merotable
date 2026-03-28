<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            $table->string('invoice_number', 50)->unique();

            $table->foreignId('restaurant_id')
                ->constrained('restaurants')
                ->cascadeOnDelete();

            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();

            $table->foreignId('table_id')
                ->constrained('tables')
                ->cascadeOnDelete();

            $table->decimal('subtotal', 10, 2)->default(0.00);
            $table->decimal('tax_amount', 10, 2)->default(0.00);
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->decimal('service_charge', 10, 2)->default(0.00);
            $table->decimal('total_amount', 10, 2);

            $table->enum('payment_method', ['cash', 'card', 'esewa', 'khalti']);

            $table->enum('payment_status', ['pending', 'paid', 'failed'])
                ->default('paid');

            $table->timestamp('paid_at')->nullable();

            $table->timestamps();

            $table->index('restaurant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};