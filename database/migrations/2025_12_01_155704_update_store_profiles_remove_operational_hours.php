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
        Schema::table('store_profiles', function (Blueprint $table) {
            // Add new column for product image
            $table->string('product_image')->nullable()->after('banner');

            // Drop operational hours columns
            $table->dropColumn([
                'is_senin',
                'open_senin',
                'close_senin',
                'is_selasa',
                'open_selasa',
                'close_selasa',
                'is_rabu',
                'open_rabu',
                'close_rabu',
                'is_kamis',
                'open_kamis',
                'close_kamis',
                'is_jumat',
                'open_jumat',
                'close_jumat',
                'is_sabtu',
                'open_sabtu',
                'close_sabtu',
                'is_minggu',
                'open_minggu',
                'close_minggu',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_profiles', function (Blueprint $table) {
            // Remove product_image
            $table->dropColumn('product_image');

            // Re-add operational hours columns
            $table->boolean('is_senin')->default(false);
            $table->time('open_senin')->nullable();
            $table->time('close_senin')->nullable();

            $table->boolean('is_selasa')->default(false);
            $table->time('open_selasa')->nullable();
            $table->time('close_selasa')->nullable();

            $table->boolean('is_rabu')->default(false);
            $table->time('open_rabu')->nullable();
            $table->time('close_rabu')->nullable();

            $table->boolean('is_kamis')->default(false);
            $table->time('open_kamis')->nullable();
            $table->time('close_kamis')->nullable();

            $table->boolean('is_jumat')->default(false);
            $table->time('open_jumat')->nullable();
            $table->time('close_jumat')->nullable();

            $table->boolean('is_sabtu')->default(false);
            $table->time('open_sabtu')->nullable();
            $table->time('close_sabtu')->nullable();

            $table->boolean('is_minggu')->default(false);
            $table->time('open_minggu')->nullable();
            $table->time('close_minggu')->nullable();
        });
    }
};
