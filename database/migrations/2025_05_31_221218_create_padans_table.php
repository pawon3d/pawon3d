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
        Schema::create('hitungs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('hitung_number')->unique();
            $table->enum('action', ['Hitung Persediaan', 'Catat Persediaan Rusak', 'Catat Persediaan Hilang'])->nullable();
            $table->string('note')->nullable();
            $table->string('status')->default('Draft');
            $table->date('hitung_date')->nullable();
            $table->date('hitung_date_finish')->nullable();
            $table->boolean('is_start')->default(false);
            $table->boolean('is_finish')->default(false);
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->decimal('loss_grand_total', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hitungs');
    }
};
