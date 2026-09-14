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
        Schema::table('pension.unique_app_ben_ids', function (Blueprint $table) {
            $table->unsignedBigInteger('module_id')->nullable()->after('scheme_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pension.unique_app_ben_ids', function (Blueprint $table) {
            $table->dropColumn('module_id');
        });
    }
};
