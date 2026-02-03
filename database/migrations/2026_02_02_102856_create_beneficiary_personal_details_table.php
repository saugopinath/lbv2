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
            $table->date('app_date')->nullable();
            $table->date('ds_date')->nullable();
            $table->date('dob')->nullable();

            // Personal info
            $table->integer('age')->nullable();
            $table->string('full_name', 150)->nullable();
            $table->string('mfname', 150)->nullable();   // mother/father?
            $table->string('ffname', 150)->nullable();
            $table->string('sfname', 150)->nullable();

            // Contact
            $table->string('email_id', 150)->nullable();
            // Application meta
            $table->string('app_type', 50)->nullable();
            $table->string('reg_no', 100)->nullable();
            // Social details
            $table->integer('mar_statu')->nullable();
            $table->integer('caste')->nullable();
            $table->string('cas_cer_no', 100)->nullable();
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
