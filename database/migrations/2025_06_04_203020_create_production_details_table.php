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
        Schema::create('production_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('production_id')->nullable();
            $table->foreign('production_id')->references('id')->on('productions')->onDelete('cascade');
            $table->uuid('product_id')->nullable();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->decimal('quantity_plan', 8, 0)->default(0);
            $table->decimal('quantity_get', 8, 0)->default(0);
            $table->decimal('quantity_fail', 8, 0)->default(0);
            $table->decimal('cycle', 2, 0)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_details');
    }
};
