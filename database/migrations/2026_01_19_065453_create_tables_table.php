<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tables', function (Blueprint $table) {
            $table->id();

            $table->foreignId('restaurant_id')
                ->constrained()
                ->cascadeOnDelete();

            // 1. Grouping by Area (e.g., Indoor, Terrace)
            $table->string('area_name')->default('Main Hall')->index();

            $table->string('table_number', 5);
            $table->enum('status', ['available', 'occupied', 'reserved'])->default('available');

            $table->timestamps();

            // Combination of restaurant_id and table_number must be unique
            $table->unique(['restaurant_id', 'table_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tables');
    }
};
