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
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 50);
            $table->string('description', 50)->nullable();
            $table->decimal('price', 10, 0)->default(0);
            $table->decimal('stock', 10, 0)->default(0);
            $table->string('method', 50)->nullable();
            $table->string('product_image', 255)->nullable();
            $table->boolean('is_recipe')->default(false);
            $table->boolean('is_active')->default(false);
            $table->boolean('is_recommended')->default(false);
            $table->boolean('is_other')->default(false);
            $table->boolean('is_many')->default(false);
            $table->decimal('pcs', 10, 0)->default(0);
            $table->decimal('capital', 10, 0)->default(0);
            $table->decimal('pcs_price', 10, 0)->default(0);
            $table->decimal('pcs_capital', 10, 0)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
