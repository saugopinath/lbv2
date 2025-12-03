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
        Schema::table('lb_scheme.beneficiary_personals', function (Blueprint $table) {
            $table->date('ds_phase')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lb_scheme.beneficiary_personals', function (Blueprint $table) {
            $table->dropColumn(
                'ds_phase'
            );
        });
    }
};
