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
        Schema::table('pension.caste_modification_infos', function (Blueprint $table) {
            $table->string('module_id')->nullable()->after('request_id');
            $table->integer('current_step_id')->nullable()->after('module_id');
            $table->integer('current_rank')->nullable()->after('current_step_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pension.caste_modification_infos', function (Blueprint $table) {
            $table->dropColumn(['module_id', 'current_step_id', 'current_rank']);
        });
    }
};
