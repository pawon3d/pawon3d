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
        Schema::create('materials', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 50);
            $table->string('description', 255)->nullable();
            $table->string('image')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('status', 20)->default('kosong');
            $table->boolean('is_active')->default(false);
            $table->boolean('is_recipe')->default(false);
            $table->decimal('minimum', 15, 5)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
