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
        Schema::table('scheme_tab_form_fields', function (Blueprint $table) {
            $table->smallInteger('is_syncable')->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scheme_tab_form_fields', function (Blueprint $table) {
            $table->dropColumn('is_syncable');
        });
    }
};
