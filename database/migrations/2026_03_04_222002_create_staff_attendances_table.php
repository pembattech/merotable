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
        Schema::create('staff_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')
                ->constrained('restaurants')
                ->cascadeOnDelete();

            $table->foreignId('staff_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->date('date');
            $table->timestamp('check_in')->nullable();
            $table->timestamp('check_out')->nullable();

            // Store hours in decimal (ex: 8.5 hours)
            $table->decimal('total_hours', 5, 2)->nullable();

            // present | absent | late
            $table->enum('status', ['present', 'absent', 'late'])
                ->default('present');

            // Prevent duplicate attendance for same staff on same date
            $table->unique(['staff_id', 'date']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_attendances');
    }
};
