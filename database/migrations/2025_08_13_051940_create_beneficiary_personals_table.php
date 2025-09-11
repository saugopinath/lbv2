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
         Schema::create('pension.beneficiary_personals', function (Blueprint $table) {
           
            $table->id('application_id')->unique();
            $table->unsignedBigInteger('beneficiary_id')->unique();
            $table->smallInteger('district_id');
            $table->smallInteger('block_id')->nullable();
            $table->mediumInteger('sub_division_id')->nullable();
            $table->Integer('municipality_id')->nullable();
            $table->Integer('ward_id')->nullable();
            $table->Integer('panchayat_id')->nullable();
            $table->string('full_name');
            $table->date('dob');
            $table->string('mobile_no');
            $table->string('email')->nullable();
            // $table->smallInteger('gender');
            $table->smallInteger('caste');
            $table->smallInteger('next_level_role_id');
            $table->string('caste_certificate_no')->nullable();
            $table->smallInteger('marital_status');
            $table->smallInteger('entry_type');
            $table->boolean('is_final_submit');
            $table->boolean('is_faulty');
            $table->date('ds_date')->nullable();
            $table->string('ds_registration_no')->nullable();
            $table->Integer('created_by');

            $table->foreign('beneficiary_id', 'beneficiary_id_fk')->references('beneficiary_id')->on('pension.unique_app_ben_ids');
            $table->foreign('application_id', 'application_id_fk')->references('application_id')->on('pension.unique_app_ben_ids');

            $table->foreign('created_by','user_id_fk')->references('id')->on('users');
            $table->foreign('district_id','district_id_fk')->references('id')->on('districts');
            $table->foreign('block_id','block_id_fk')->references('id')->on('blocks');
            $table->foreign('sub_division_id','sub_division_id_fk')->references('id')->on('subdivisions');
            $table->foreign('municipality_id','municipality_id_fk')->references('id')->on('municipalities');
            $table->foreign('ward_id','ward_id_fk')->references('id')->on('wards');
            $table->foreign('panchayat_id','panchayat_id_fk')->references('id')->on('panchayats');
            $table->foreign('caste','caste_id_fk')->references('id')->on('codemasters');
            $table->foreign('next_level_role_id','next_level_role_id_fk')->references('id')->on('codemasters');
            $table->foreign('marital_status','marital_status_fk')->references('id')->on('codemasters');
            $table->foreign('entry_type','entry_type_fk')->references('id')->on('codemasters');
            $table->timestamps();
            $table->index(['district_id','block_id']);
            $table->index(['district_id','sub_division_id']);
            $table->index(['district_id','municipality_id','ward_id']);
            $table->index(['district_id','block_id','panchayat_id']);
            $table->index('application_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lb_scheme.beneficiary_personals');
    }
};
