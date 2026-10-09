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
        Schema::table('dynamic_workflow_modules', function (Blueprint $table) {
            $table->string('workflow_type', 20)->default('normal')->after('module_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dynamic_workflow_modules', function (Blueprint $table) {
            $table->dropColumn('workflow_type');
        });
    }
};
