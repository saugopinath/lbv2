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
        Schema::table('lb_scheme.beneficiary_banks', function (Blueprint $table) {
            $table->string('bankpassbook_name')->nullable();
            $table->smallInteger('app_gen_score')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lb_scheme.beneficiary_banks', function (Blueprint $table) {
            $table->dropColumn('bankpassbook_name');
            $table->dropColumn('app_gen_score');
        });
    }
};
