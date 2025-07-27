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
        Schema::create('lb_scheme.draft_beneficiary_contacts', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('application_id');
            $table->foreign('application_id', 'application_id_fk')->references('application_id')->on('lb_scheme.unique_app_ben_ids')->onDelete('cascade');
            $table->integer('beneficiary_id');
            $table->smallInteger('district_id');
            $table->smallInteger('rural_urban_id');
            $table->smallInteger('block_id');
            $table->Integer('municipality_id');
            $table->Integer('ward_id');
            $table->Integer('panchayat_id');
            $table->string('police_station',200);
            $table->string('village_town_city',300);
            $table->string('house_premise_no',300);
            $table->string('post_office',300);
            $table->char('pincode',8);
            $table->Integer('residency_period');
            $table->Integer('created_by');
            $table->foreign('created_by','user_id_fk')->references('id')->on('public.users');
            // $table->foreign('application_id','application_id_fk')->references('application_id')->on('lb_scheme.draft_beneficiary_personals');
            $table->foreign('district_id','district_id_fk')->references('id')->on('public.districts');
            $table->foreign('municipality_id','municipality_id_fk')->references('id')->on('public.municipalities');
            $table->foreign('ward_id','ward_id_fk')->references('id')->on('public.wards');
            $table->foreign('block_id','block_id_fk')->references('id')->on('public.blocks');
            $table->foreign('panchayat_id','panchayat_id_fk')->references('id')->on('public.panchayats');
            $table->timestamps();
            $table->index(['district_id','block_id'], 'draft_beneficiary_contacts_district_id_block_id_index');
            $table->index(['district_id','municipality_id'], 'draft_beneficiary_contacts_district_id_municipality_id_index');
            $table->index(['district_id','municipality_id','ward_id'], 'draft_beneficiary_contacts_district_id_municipality_id_ward_id_index');
            $table->index(['district_id','block_id','panchayat_id'], 'draft_beneficiary_contacts_district_id_block_id_panchayat_id_index');
            // $table->index('application_id', 'draft_beneficiary_contacts_application_id_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lb_scheme.draft_beneficiary_contacts');
    }
};
