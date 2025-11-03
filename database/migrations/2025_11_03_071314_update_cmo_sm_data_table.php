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
        Schema::table('cmo.cmo_sm_data', function (Blueprint $table) {
            $table->string('redressed_status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cmo.cmo_sm_data', function (Blueprint $table) {
            $table->dropColumn('redressed_status');
        });
    }
};
