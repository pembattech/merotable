
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();

            $table->string('name', 150);
            $table->string('owner_name', 150);
            $table->string('email', 150)->unique();
            $table->string('password', 255);
            $table->string('slug')->unique();
            $table->string('contact_number', 50);

            $table->enum('status', ['active', 'inactive', 'blocked', 'expired', 'pending'])
                  ->default('pending')
                  ->index();

            $table->text('description')->nullable();
            $table->string('address', 255)->nullable();
            $table->string('logo')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurants');
    }
};