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
        Schema::table('other_costs', function (Blueprint $table) {
            $table->uuid('type_cost_id')->nullable();
            $table->foreign('type_cost_id')->references('id')->on('type_costs')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('other_costs', function (Blueprint $table) {
            $table->dropForeign(['type_cost_id']);
            $table->dropColumn('type_cost_id');
        });
    }
};
