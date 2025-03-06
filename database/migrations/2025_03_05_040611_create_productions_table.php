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
            $table->uuid('product_id')->nullable();
            $table->uuid('transaction_id')->nullable();
            $table->uuid('transaction_detail_id')->nullable();
            $table->enum('type', ['siap beli', 'pesanan'])->nullable();
            $table->decimal('count', 3, 0)->nullable();
            $table->string('status', 20)->nullable();
            $table->decimal('time', 10, 0)->nullable();
            $table->decimal('quantity', 10, 0)->nullable();
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
            $table->foreign('transaction_detail_id')->references('id')->on('transaction_details')->onDelete('cascade');
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
