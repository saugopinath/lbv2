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

            // Add marked_data (smallint)
            $table->smallInteger('marked_data')
                  ->nullable()
                  ->after('next_level_role_id');

            // Add jnmp_marked (smallint)
            $table->smallInteger('jnmp_marked')
                  ->nullable()
                  ->after('marked_data');

            // Add jnmp_remarks (varchar)
            $table->string('jnmp_remarks')
                  ->nullable()
                  ->after('jnmp_marked');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lb_scheme.beneficiary_personals', function (Blueprint $table) {
            $table->dropColumn(['marked_data', 'jnmp_marked', 'jnmp_remarks']);
        });
    }
};
