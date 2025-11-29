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
        Schema::create('expense_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('expense_id')->nullable();
            $table->uuid('material_id')->nullable();
            $table->uuid('unit_id')->nullable();
            $table->decimal('quantity_expect', 15, 5)->default(0);
            $table->decimal('quantity_get', 15, 5)->default(0);
            $table->boolean('is_quantity_get')->default(false);
            $table->decimal('price_expect', 10, 2)->default(0);
            $table->decimal('price_get', 10, 2)->default(0);
            $table->decimal('total_expect', 10, 2)->default(0);
            $table->decimal('total_actual', 10, 2)->default(0);
            $table->date('expiry_date')->nullable();
            $table->timestamps();

            $table->foreign('expense_id')->references('id')->on('expenses')->onDelete('cascade');
            $table->foreign('material_id')->references('id')->on('materials')->onDelete('cascade');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_details');
    }
};
