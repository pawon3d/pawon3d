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
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('receipt_number', 30)->nullable()->unique();
            $table->uuid('transaction_id')->nullable();
            $table->uuid('payment_channel_id')->nullable();
            $table->string('payment_method', 20)->nullable();
            $table->string('payment_group', 20)->nullable();
            $table->decimal('paid_amount', 15, 0)->default(0);
            $table->string('image')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
            $table->foreign('payment_channel_id')->references('id')->on('payment_channels')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
