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
        Schema::table('scheme_tab_mappings', function (Blueprint $table) {
            $table->boolean('show_modal_card')->default(true)->after('modal_placement');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scheme_tab_mappings', function (Blueprint $table) {
            $table->dropColumn('show_modal_card');
        });
    }
};
