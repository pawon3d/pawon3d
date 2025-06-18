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
        Schema::create('material_batches', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('material_id')->nullable();
            $table->uuid('unit_id')->nullable();
            $table->string('batch_number');
            $table->date('date')->nullable();
            $table->decimal('batch_quantity', 15, 2)->default(0);
            $table->timestamps();

            $table->foreign('material_id')->references('id')->on('materials')->onDelete('cascade');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_batches');
    }
};