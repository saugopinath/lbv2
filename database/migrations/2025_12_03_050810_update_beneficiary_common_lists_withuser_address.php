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
        Schema::table('lb_scheme.beneficiary_common_lists', function (Blueprint $table) {
            $table->smallInteger('next_level_role_id')->nullable();
            $table->smallInteger('cd_district_id')->nullable();
            $table->smallInteger('cd_rural_urban_id')->nullable();
            $table->smallInteger('cd_block_muni_id')->nullable();
            $table->Integer('cd_gp_ward_id')->nullable();
            // $table->mediumInteger('cd_sub_division_id')->nullable();
            // $table->Integer('cd_municipality_id')->nullable();
            // $table->Integer('cd_panchayat_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lb_scheme.beneficiary_common_lists', function (Blueprint $table) {
            $table->dropColumn([
                'next_level_role_id',
                'cd_district_id',
                'cd_rural_urban_id',
                'cd_block_muni_id',
                'cd_gp_ward_id',
                // 'cd_sub_division_id',
                // 'cd_municipality_id',
                // 'cd_panchayat_id'
            ]);
        });
    }
};
