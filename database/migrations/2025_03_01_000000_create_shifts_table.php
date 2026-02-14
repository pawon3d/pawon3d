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
        Schema::create('shifts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('shift_number', 50)->unique();
            $table->uuid('opened_by')->nullable();
            $table->uuid('closed_by')->nullable();

            $table->foreign('opened_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('closed_by')->references('id')->on('users')->nullOnDelete();
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->string('status', 50)->default('closed');
            $table->decimal('initial_cash', 10, 0)->default(0);
            $table->decimal('final_cash', 10, 0)->default(0);
            $table->decimal('total_sales', 10, 2)->default(0);
            $table->decimal('total_refunds', 10, 2)->default(0);
            $table->decimal('total_discounts', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
