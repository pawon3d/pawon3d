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
        Schema::create('store_profiles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('logo')->nullable();
            $table->string('name', 100)->nullable();
            $table->string('tagline', 200)->nullable();
            $table->string('type', 50)->nullable();

            $table->string('banner')->nullable();
            $table->string('product', 100)->nullable();
            $table->text('description')->nullable();

            $table->string('building', 100)->nullable();
            $table->string('location', 100)->nullable();
            $table->string('address')->nullable();
            $table->string('contact', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();

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

            $table->string('social_instagram', 100)->nullable();
            $table->string('social_facebook', 100)->nullable();
            $table->string('social_whatsapp', 20)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_profiles');
    }
};
