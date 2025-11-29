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
        Schema::create('ingredient_category_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('ingredient_category_id')->nullable();
            $table->uuid('material_id')->nullable();

            $table->foreign('ingredient_category_id')
                ->references('id')
                ->on('ingredient_categories')
                ->onDelete('cascade');
            $table->foreign('material_id')
                ->references('id')
                ->on('materials')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingredient_category_details');
    }
};
