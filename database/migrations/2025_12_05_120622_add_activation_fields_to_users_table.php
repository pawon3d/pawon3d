<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_active')->default(false)->after('image');
            $table->string('invitation_token', 64)->nullable()->after('is_active');
            $table->timestamp('invitation_sent_at')->nullable()->after('invitation_token');
            $table->timestamp('activated_at')->nullable()->after('invitation_sent_at');
        });

        // Aktifkan semua user yang sudah ada
        DB::table('users')->update(['is_active' => true, 'activated_at' => now()]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'invitation_token', 'invitation_sent_at', 'activated_at']);
        });
    }
};
