
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Store serving sizes and their individual prices.
     * Examples: Half, Full, Small, Medium, Large.
     */
    public function up(): void
    {
        Schema::create('menu_item_sizes', function (Blueprint $table) {
            $table->id();

            // The menu item associated with this serving size.
            $table->foreignId('menu_item_id')
                ->constrained()
                ->cascadeOnDelete();

            // Display name of the size, e.g. Half or Large.
            $table->string('name', 50);

            // Short identifier for UI/API use, e.g. half, full, sml.
            $table->string('code', 20);

            // Price charged for this particular size.
            $table->decimal('price', 10, 2);

            // Controls the order in which sizes appear in the UI.
            $table->unsignedSmallInteger('sort_order')->default(0);

            // Whether this size can currently be ordered.
            $table->boolean('is_available')->default(true);

            $table->timestamps();

            // Prevent duplicate size names or codes for one menu item.
            $table->unique(['menu_item_id', 'name']);
            $table->unique(['menu_item_id', 'code']);
        });
    }

    /**
     * Remove the menu item sizes table.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_item_sizes');
    }
};
