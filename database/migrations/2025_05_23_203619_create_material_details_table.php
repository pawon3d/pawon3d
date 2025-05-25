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
        Schema::create('material_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('material_id')->nullable();
            $table->uuid('unit_id')->nullable();
            $table->boolean('is_main')->default(false);
            $table->decimal('quantity', 15, 5)->default(0);
            $table->decimal('supply_quantity', 15, 5)->default(0);
            $table->decimal('supply_price', 10, 2)->default(0);

            $table->foreign('material_id')
                ->references('id')
                ->on('materials')
                ->onDelete('cascade');
            $table->foreign('unit_id')
                ->references('id')
                ->on('units')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_details');
    }
};
