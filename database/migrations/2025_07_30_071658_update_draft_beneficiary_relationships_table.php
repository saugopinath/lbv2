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
        Schema::table('lb_scheme.draft_beneficiary_relationships', function (Blueprint $table) {
            $table->string('code', 5);
            $table->foreign('code', 'code_fk')->references('code')->on('codemasters');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lb_scheme.draft_beneficiary_relationships', function (Blueprint $table) {
            $table->dropForeign('code_fk');
            $table->dropColumn('code');
        });
    }
};
