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
         Schema::table('lb_scheme.draft_beneficiary_personals', function (Blueprint $table) {
            $table->string('email')->nullable();
        });

         Schema::table('lb_scheme.beneficiary_personals', function (Blueprint $table) {
            $table->string('email')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('lb_scheme.draft_beneficiary_personals', function (Blueprint $table) {
            $table->dropColumn('email');
        });


        Schema::table('lb_scheme.beneficiary_personals', function (Blueprint $table) {
            $table->dropColumn('email');
            });
    }
};
