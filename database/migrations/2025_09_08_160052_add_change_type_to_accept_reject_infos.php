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
        Schema::table('applicant_incomplet_deatils', function (Blueprint $table) {
            $table->smallInteger('change_type')->nullable()->after('request_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applicant_incomplet_deatils', function (Blueprint $table) {
            $table->dropColumn('change_type');
        });
    }
};
