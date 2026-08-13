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
        Schema::create('lb_scheme.beneficiary_personal_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('scheme_id');
            $table->bigInteger('application_id')->unique();
            $table->bigInteger('beneficiary_id')->nullable();
            // Workflow / role
            $table->bigInteger('next_level_role_id')->nullable();
            // Dates
            $table->date('application_date')->nullable();
            $table->date('ds_date')->nullable();
            $table->date('dob')->nullable();

            // Personal info
            $table->integer('age')->nullable();
            $table->string('beneficiary_name', 150)->nullable();
            $table->string('ben_mother_name', 150)->nullable();
            $table->string('ben_father_name', 150)->nullable();
            $table->string('ben_spouse_name', 150)->nullable();

            // Contact
            $table->string('email', 150)->nullable();
            // Application meta
            $table->string('application_type', 50)->nullable();
            $table->string('ds_registration_no', 100)->nullable();
            // Social details
            $table->integer('marital_status')->nullable();
            $table->integer('caste')->nullable();
            $table->string('caste_cer_no', 100)->nullable();
            $table->smallInteger('is_final')->default(0);
            $table->integer('created_by_dist_code')->nullable();
            $table->integer('created_by_local_body_code')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            // JSONB extra data
            $table->jsonb('other_details')->nullable();
            // Audit
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lb_scheme.beneficiary_personal_details');
    }
};
