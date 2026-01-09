<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lb_scheme.ben_reject_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('application_id');
            $table->unsignedBigInteger('beneficiary_id')->nullable();
            $table->Integer('created_by');
            $table->jsonb('personal_details')->nullable();      // from draft_ben_personal
            $table->jsonb('contact_details')->nullable();       // from draft_ben_contact
            $table->jsonb('bank_details')->nullable();          // from draft_ben_bank
            $table->jsonb('declaration_details')->nullable();   // from draft_ben_declaration
            $table->jsonb('relationship_details')->nullable();  // from draft_ben_relationship
            $table->jsonb('aadhar_details')->nullable();        // from draft_ben_adhar
            $table->smallInteger('district_id');
            $table->smallInteger('block_id')->nullable();
            $table->mediumInteger('sub_division_id')->nullable();
            $table->Integer('municipality_id')->nullable();
            $table->Integer('ward_id')->nullable();
            $table->Integer('panchayat_id')->nullable();
            $table->foreign('application_id', 'application_id_fk')->references('sourceable_id')
                ->on('lb_scheme.beneficiary_common_lists')->onDelete('cascade');
            $table->foreign('beneficiary_id', 'beneficiary_id_fk')->references('beneficiary_id')
                ->on('lb_scheme.unique_app_ben_ids');
            $table->foreign('district_id', 'district_id_fk')->references('id')->on('public.districts');
            $table->foreign('sub_division_id', 'sub_division_id_fk')->references('id')->on('public.subdivisions');
            $table->foreign('municipality_id', 'municipality_id_fk')->references('id')->on('public.municipalities');
            $table->foreign('ward_id', 'ward_id_fk')->references('id')->on('public.wards');
            $table->foreign('block_id', 'block_id_fk')->references('id')->on('public.blocks');
            $table->foreign('panchayat_id', 'panchayat_id_fk')->references('id')->on('public.panchayats');
            $table->foreign('created_by', 'user_id_fk')->references('id')->on('users');
            $table->index(['district_id', 'block_id']);
            $table->index(['district_id', 'municipality_id']);
            $table->index(['district_id', 'sub_division_id']);
            $table->index(['district_id', 'municipality_id', 'ward_id']);
            $table->index(['district_id', 'block_id', 'panchayat_id']);
            $table->index('application_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lb_scheme.ben_reject_details');
    }
};
