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
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->nullable();
            $table->uuid('customer_id')->nullable();
            $table->string('invoice_number', 30)->unique();
            $table->string('name', 50)->nullable()->default('Unregistered');
            $table->string('phone', 20)->nullable();
            $table->date('date')->nullable();
            $table->time('time')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('note')->nullable();
            $table->string('payment_status', 20)->nullable();
            $table->string('status', 20)->nullable();
            $table->string('method', 20)->default('pesanan-reguler');
            $table->decimal('total_amount', 15, 0)->nullable();
            $table->integer('points_used')->default(0);
            $table->decimal('points_discount', 15, 2)->default(0);
            $table->decimal('total_refund', 15, 0)->default(0);
            $table->text('cancel_reason')->nullable();
            $table->string('cancel_proof_image')->nullable();
            $table->uuid('created_by_shift')->nullable();
            $table->uuid('refund_by_shift')->nullable();
            $table->uuid('cancelled_by_shift')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
            $table->foreign('created_by_shift')->references('id')->on('shifts')->onDelete('cascade');
            $table->foreign('refund_by_shift')->references('id')->on('shifts')->onDelete('cascade');
            $table->foreign('cancelled_by_shift')->references('id')->on('shifts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
