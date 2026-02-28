<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('restaurant_add_ons', function (Blueprint $table) {
            $table->id();

            $table->foreignId('restaurant_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('add_on_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->integer('quantity')->default(1);

            $table->timestamp('starts_at')->useCurrent();
            $table->timestamp('expires_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_add_ons');
    }
};