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
        Schema::create('productions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('production_number')->nullable();
            $table->uuid('transaction_id')->nullable();
            $table->string('method')->nullable();
            $table->date('date')->nullable();
            $table->time('time')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('note')->nullable();
            $table->string('status', 20)->default('Draft');
            $table->boolean('is_start')->default(false);
            $table->boolean('is_finish')->default(false);
            $table->timestamps();

            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productions');
    }
};
