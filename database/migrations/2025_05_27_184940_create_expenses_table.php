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
        Schema::create('expenses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('expense_number')->unique();
            $table->date('expense_date')->nullable();
            $table->date('end_date')->nullable();
            $table->uuid('supplier_id')->nullable();
            $table->string('note')->nullable();
            $table->string('status')->default('Draft');
            $table->decimal('grand_total_expect', 10, 2)->default(0);
            $table->decimal('grand_total_actual', 10, 2)->default(0);
            $table->boolean('is_start')->default(false);
            $table->boolean('is_finish')->default(false);
            $table->timestamps();

            $table->foreign('supplier_id')->references('id')->on('suppliers')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};