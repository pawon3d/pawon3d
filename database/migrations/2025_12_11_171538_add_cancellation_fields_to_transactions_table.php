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
            $table->text('cancel_reason')->nullable()->after('points_discount');
            $table->string('cancel_proof_image')->nullable()->after('cancel_reason');
            $table->foreignUuid('cancelled_by_shift')->nullable()->after('cancel_proof_image')->constrained('shifts')->onDelete('cascade');
            $table->timestamp('cancelled_at')->nullable()->after('cancelled_by_shift');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['cancelled_by_shift']);
            $table->dropColumn(['cancel_reason', 'cancel_proof_image', 'cancelled_by_shift', 'cancelled_at']);
        });
    }
};
