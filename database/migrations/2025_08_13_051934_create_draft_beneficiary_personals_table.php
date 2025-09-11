<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pension.draft_beneficiary_personals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('application_id')->unique();
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
            $table->foreign('application_id', 'application_id_fk')->references('application_id')->on('pension.unique_app_ben_ids');
            $table->foreign('created_by', 'user_id_fk')->references('id')->on('public.users');

            $table->foreign('district_id', 'district_id_fk')->references('id')->on('public.districts');
            $table->foreign('block_id', 'block_id_fk')->references('id')->on('public.blocks');
            $table->foreign('sub_division_id', 'sub_division_id_fk')->references('id')->on('public.subdivisions');
            $table->foreign('municipality_id', 'municipality_id_fk')->references('id')->on('public.municipalities');
            $table->foreign('ward_id', 'ward_id_fk')->references('id')->on('public.wards');
            $table->foreign('panchayat_id', 'panchayat_id_fk')->references('id')->on('public.panchayats');
            $table->foreign('caste', 'caste_id_fk')->references('id')->on('public.codemasters');
            $table->foreign('next_level_role_id', 'next_level_role_id_fk')->references('id')->on('public.codemasters');
            $table->foreign('marital_status', 'marital_status_fk')->references('id')->on('public.codemasters');
            $table->foreign('entry_type', 'entry_type_fk')->references('id')->on('public.codemasters');
            $table->timestamps();
            $table->index(['district_id', 'block_id'], 'draft_beneficiary_personals_district_id_block_id_index');
            $table->index(['district_id', 'sub_division_id'], 'draft_beneficiary_personals_district_id_sub_division_id_index');
            $table->index(['district_id', 'municipality_id', 'ward_id'], 'draft_beneficiary_personals_district_id_municipality_id_ward_id_index');
            $table->index(['district_id', 'block_id', 'panchayat_id'], 'draft_beneficiary_personals_district_id_block_id_panchayat_id_index');
            $table->index('application_id', 'draft_beneficiary_personals_application_id_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lb_scheme.draft_beneficiary_personals');
    }
};
