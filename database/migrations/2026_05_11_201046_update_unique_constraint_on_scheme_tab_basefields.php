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
        Schema::table('scheme_tab_basefields', function (Blueprint $table) {
            $table->dropUnique('scheme_tab_basefields_field_id_unique');
            $table->unique(['scheme_id', 'tab_code', 'field_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scheme_tab_basefields', function (Blueprint $table) {
            $table->dropUnique(['scheme_id', 'tab_code', 'field_id']);
            $table->unique('field_id');
        });
    }
};
