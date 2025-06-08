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
        Schema::create('hitung_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('hitung_id')->nullable();
            $table->uuid('material_id')->nullable();
            $table->uuid('unit_id')->nullable();
            $table->decimal('quantity_expect', 15, 2)->default(0);
            $table->decimal('quantity_actual', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->decimal('loss_total', 15, 2)->default(0);
            $table->timestamps();

            $table->foreign('hitung_id')->references('id')->on('hitungs')->onDelete('cascade');
            $table->foreign('material_id')->references('id')->on('materials')->onDelete('cascade');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hitung_details');
    }
};
