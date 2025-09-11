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
         Schema::create('pension.beneficiary_contacts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('beneficiary_id')->unique();;

            $table->unsignedBigInteger('application_id')->unique();;
            $table->smallInteger('district_id');
            $table->smallInteger('rural_urban_id');
            $table->smallInteger('block_id')->nullable();
            $table->mediumInteger('sub_division_id')->nullable();
            $table->Integer('municipality_id')->nullable();
            $table->Integer('ward_id')->nullable();
            $table->Integer('panchayat_id')->nullable();
            $table->string('police_station',200);
            $table->string('village_town_city',300);
            $table->string('house_premise_no',300);
            $table->string('post_office',300);
            $table->char('pincode',8);
            $table->Integer('residency_period');
            $table->Integer('created_by');
            $table->foreign('created_by','user_id_fk')->references('id')->on('users');
             $table->foreign('beneficiary_id', 'beneficiary_id_fk')->references('beneficiary_id')->on('pension.beneficiary_personals')->onDelete('cascade');
            $table->foreign('district_id','district_id_fk')->references('id')->on('districts');
            $table->foreign('sub_division_id', 'sub_division_id_fk')->references('id')->on('subdivisions');
            $table->foreign('municipality_id','municipality_id_fk')->references('id')->on('municipalities');
            $table->foreign('ward_id','ward_id_fk')->references('id')->on('wards');
            $table->foreign('block_id','block_id_fk')->references('id')->on('blocks');
            $table->foreign('panchayat_id','panchayat_id_fk')->references('id')->on('panchayats');
            $table->timestamps();
            $table->index(['district_id','block_id'],'beneficiary_contacts_district_id_block_id_index');
            $table->index(['district_id','municipality_id'],'beneficiary_contacts_district_id_municipality_id_index');
            $table->index(['district_id', 'sub_division_id'], 'beneficiary_contacts_district_id_sub_division_id_index');
            $table->index(['district_id','municipality_id','ward_id'],'beneficiary_contacts_district_id_municipality_id_ward_id_index');
            $table->index(['district_id','block_id','panchayat_id'],'beneficiary_contacts_district_id_block_id_panchayat_id_index');
            $table->index('application_id', 'beneficiary_contacts_application_id_index');
            $table->index('beneficiary_id', 'beneficiary_contacts_beneficiary_id_index');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lb_scheme.beneficiary_contacts');
    }
};