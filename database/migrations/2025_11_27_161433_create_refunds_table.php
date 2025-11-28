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
        Schema::create('refunds', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('transaction_id')->constrained()->cascadeOnDelete();
            $table->string('reason')->nullable(); // Alasan refund (gosong, dll)
            $table->string('proof_image')->nullable(); // Bukti foto
            $table->string('refund_method'); // tunai, transfer
            $table->foreignUuid('payment_channel_id')->nullable()->constrained()->cascadeOnDelete(); // Bank jika transfer
            $table->string('account_number')->nullable(); // Nomor rekening customer
            $table->decimal('total_amount', 10, 0)->default(0); // Total refund
            $table->foreignUuid('refund_by_shift')->nullable()->constrained('shifts')->cascadeOnDelete();
            $table->datetime('refunded_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refunds');
    }
};
