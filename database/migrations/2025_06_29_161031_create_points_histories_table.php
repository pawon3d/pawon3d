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
        Schema::create('points_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('phone', 20)->nullable();
            $table->string('action_id', 30)->nullable();
            $table->string('action', 50)->nullable();
            $table->integer('points')->default(0);
            $table->uuid('transaction_id')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();

            $table->foreign('phone')
                ->references('phone')
                ->on('customers')
                ->onDelete('cascade');
            $table->foreign('transaction_id')
                ->references('id')
                ->on('transactions')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('points_histories');
    }
};
