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
        Schema::table('transactions', function (Blueprint $table) {
            $table->decimal('total_refund', 10, 0)->default(0);
            $table->uuid('created_by_shift')->nullable();
            $table->uuid('refund_by_shift')->nullable();
            $table->foreign('created_by_shift')->references('id')->on('shifts')->onDelete('cascade');
            $table->foreign('refund_by_shift')->references('id')->on('shifts')->onDelete('cascade');
        });

        Schema::table('transaction_details', function (Blueprint $table) {
            $table->decimal('refund_quantity', 8, 0)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('total_refund');
            $table->dropColumn('created_by_shift');
            $table->dropColumn('refund_by_shift');
        });

        Schema::table('transaction_details', function (Blueprint $table) {
            $table->dropColumn('refund_quantity');
        });
    }
};
