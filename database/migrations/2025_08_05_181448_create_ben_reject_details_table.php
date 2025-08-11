<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lb_scheme.ben_reject_details', function (Blueprint $table) {
            $table->integer('application_id')->primary();
            $table->integer('beneficiary_id')->nullable();
            $table->string('full_name');
            $table->date('dob');
            $table->string('mobile_no')->nullable();
            $table->smallInteger('gender')->nullable();
            $table->smallInteger('caste')->nullable();
            $table->smallInteger('next_level_role_id')->nullable();
            $table->string('caste_certificate_no')->nullable();
            $table->smallInteger('marital_status')->nullable();
            $table->smallInteger('entry_type')->nullable();
            $table->boolean('is_final_submit')->nullable();
            $table->boolean('is_faulty')->nullable();
            $table->date('ds_date')->nullable();
            $table->string('ds_registration_no')->nullable();
            $table->integer('created_by')->nullable();
            $table->string('father_full_name', 200)->nullable();
            $table->string('mother_full_name', 200)->nullable();
            $table->string('spouse_full_name', 200)->nullable();
            $table->smallInteger('district_id')->nullable();
            $table->smallInteger('rural_urban_id')->nullable();
            $table->smallInteger('block_id')->nullable();
            $table->integer('municipality_id')->nullable();
            $table->integer('ward_id')->nullable();
            $table->integer('panchayat_id')->nullable();
            $table->string('police_station', 200)->nullable();
            $table->string('village_town_city', 300)->nullable();
            $table->string('house_premise_no', 300)->nullable();
            $table->string('post_office', 300)->nullable();
            $table->char('pincode', 8)->nullable();
            $table->integer('residency_period')->nullable();
            $table->char('ifsc', 11)->nullable();
            $table->char('bank_account_number', 20)->nullable();
            $table->string('encode_key')->nullable();
            $table->string('aadhar_hash')->nullable()->unique();
            $table->text('encoded_aadhar')->nullable();
            $table->smallInteger('document_type')->nullable();
            $table->text('attched_document')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('document_extension')->nullable();
            $table->string('document_mime_type')->nullable();
            $table->boolean('av_status')->default(true);
            $table->boolean('earn_monthly_remuneration')->nullable();
            $table->boolean('info_genuine_decl')->nullable();
            $table->boolean('is_resident')->nullable();
            $table->string('rejected_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign Keys
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('caste')->references('id')->on('codemasters')->nullOnDelete();
            $table->foreign('next_level_role_id')->references('id')->on('codemasters')->nullOnDelete();
            $table->foreign('marital_status')->references('id')->on('codemasters')->nullOnDelete();
            $table->foreign('entry_type')->references('id')->on('codemasters')->nullOnDelete();
            $table->foreign('district_id')->references('id')->on('districts')->nullOnDelete();
            $table->foreign('block_id')->references('id')->on('blocks')->nullOnDelete();
            $table->foreign('municipality_id')->references('id')->on('municipalities')->nullOnDelete();
            $table->foreign('ward_id')->references('id')->on('wards')->nullOnDelete();
            $table->foreign('panchayat_id')->references('id')->on('panchayats')->nullOnDelete();
            $table->foreign('ifsc')->references('code')->on('ifsccodemasters')->nullOnDelete();
            $table->foreign('document_type')->references('id')->on('codemasters')->nullOnDelete();
            $table->foreign('beneficiary_id')->references('beneficiary_id')->on('lb_scheme.beneficiary_personals')->onDelete('cascade');
            $table->foreign('application_id')->references('application_id')->on('lb_scheme.unique_app_ben_ids');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lb_scheme.ben_reject_details');
    }
};
