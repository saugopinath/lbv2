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
            $table->string('modal_placement')->default('top-right')->after('is_current_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scheme_tab_mappings', function (Blueprint $table) {
            $table->dropColumn('modal_placement');
        });
    }
};
