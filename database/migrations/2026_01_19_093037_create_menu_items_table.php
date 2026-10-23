
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates menu items for each restaurant.
     * Supports dietary preferences, popularity,
     * availability, and multiple serving sizes.
     */
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();

            // Restaurant that owns this menu item.
            $table->foreignId('restaurant_id')
                ->constrained()
                ->cascadeOnDelete();

            // Category such as Momo, Drinks, or Desserts.
            // Nullable so a menu item can exist without a category.
            $table->foreignId('category_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            // Display name of the food or drink.
            $table->string('name', 150);
            $table->string('image')->nullable();
            $table->text('description')->nullable();

            // Default price when no size is selected.
            // For sized items, this can represent the Full/Medium price.
            $table->decimal('price', 10, 2);

            // Whether the item is vegetarian.
            $table->boolean('is_vegetarian')->default(false);

            // Highlight popular or frequently ordered items.
            $table->boolean('is_popular')->default(false);

            // Whether customers and staff can order this item.
            // Set false when the item is out of stock.
            $table->boolean('is_available')->default(true);

            // Whether the item has configurable serving sizes.
            $table->boolean('has_sizes')->default(false);

            // Size configuration: hf (Half/Full),
            // sml (Small/Medium/Large), or null (no sizes).
            $table->enum('size_type', ['hf', 'sml'])
                ->nullable();

            $table->timestamps();

            // Prevent duplicate menu item names within a restaurant.
            $table->unique(['restaurant_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * Drops the menu_items table.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
