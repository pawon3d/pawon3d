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
        Schema::create('other_costs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('product_id')->nullable();
            $table->uuid('type_cost_id')->nullable();
            $table->decimal('price', 15, 2)->default(0);
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->foreign('type_cost_id')->references('id')->on('type_costs')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('other_costs');
    }
};
