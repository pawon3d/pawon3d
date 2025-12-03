<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Menambah field untuk konversi satuan dalam grup yang sama.
     * Contoh:
     * - Kilogram (base_unit_id = null, conversion_factor = 1) -> satuan dasar grup "Berat"
     * - Gram (base_unit_id = kg_id, conversion_factor = 0.001) -> 1 gram = 0.001 kg
     * - Liter (base_unit_id = null, conversion_factor = 1) -> satuan dasar grup "Volume"
     * - Mililiter (base_unit_id = liter_id, conversion_factor = 0.001) -> 1 ml = 0.001 liter
     */
    public function up(): void
    {
        Schema::table('units', function (Blueprint $table) {
            // ID satuan dasar dalam grup (null jika ini adalah satuan dasar)
            $table->string('base_unit_id')->nullable()->after('group');
            // Faktor konversi ke satuan dasar (misal: gram ke kg = 0.001)
            $table->decimal('conversion_factor', 20, 10)->default(1)->after('base_unit_id');

            $table->foreign('base_unit_id')->references('id')->on('units')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('units', function (Blueprint $table) {
            $table->dropForeign(['base_unit_id']);
            $table->dropColumn(['base_unit_id', 'conversion_factor']);
        });
    }
};
