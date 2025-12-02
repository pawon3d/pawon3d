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
        Schema::create('inventory_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('material_id')->constrained('materials')->cascadeOnDelete();
            $table->foreignUuid('material_batch_id')->nullable()->constrained('material_batches')->nullOnDelete();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action'); // belanja, terpakai, rusak, hilang, hitung, produksi
            $table->decimal('quantity_change', 15, 4)->default(0); // perubahan (+/-)
            $table->decimal('quantity_after', 15, 4)->default(0); // stok akhir batch
            $table->string('reference_type')->nullable(); // Expense, Production, Hitung, dll
            $table->string('reference_id')->nullable(); // ID referensi
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['material_id', 'created_at']);
            $table->index(['material_batch_id', 'created_at']);
            $table->index('action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_logs');
    }
};
