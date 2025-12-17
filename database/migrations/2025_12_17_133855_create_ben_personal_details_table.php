<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lb_scheme.ben_personal_details', function (Blueprint $table) {

            $table->integer('application_id')->primary();
            $table->integer('beneficiary_id')->nullable();
            $table->string('ben_fname')->nullable();
            $table->string('ben_mname')->nullable();
            $table->string('ben_lname')->nullable();
            $table->string('gender')->nullable();
            $table->date('dob')->nullable();
            $table->integer('age_ason_01012021')->nullable();
            $table->string('caste')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('father_fname')->nullable();
            $table->string('father_mname')->nullable();
            $table->string('father_lname')->nullable();
            $table->string('mother_fname')->nullable();
            $table->string('mother_mname')->nullable();
            $table->string('mother_lname')->nullable();
            $table->string('spouse_fname')->nullable();
            $table->string('spouse_mname')->nullable();
            $table->string('spouse_lname')->nullable();
            $table->decimal('pension_amount', 10, 2)->nullable();
            $table->string('ss_card_no')->nullable();
            $table->char('mobile_no', 15)->nullable();
            $table->smallInteger('scheme_id')->nullable();
            $table->integer('created_by_dist_code')->nullable();
            $table->string('created_by_level')->nullable();
            $table->integer('created_by_local_body_code')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->integer('created_by')->nullable();
            $table->string('ip_address')->nullable();
            $table->bigInteger('ss_ben_id')->nullable();
            $table->string('caste_certificate_no')->nullable();
            $table->char('aadhar_no', 12)->nullable();
            $table->smallInteger('next_level_role_id')->nullable();
            $table->smallInteger('rejected_cause')->nullable();
            $table->string('ss_full_name')->nullable();
            $table->string('comments')->nullable();
            $table->string('duare_sarkar_registration_no')->nullable();
            $table->date('duare_sarkar_date')->nullable();
            $table->string('email')->nullable();
            $table->boolean('sms_is_send')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->smallInteger('is_migrated')->nullable();
            $table->smallInteger('status')->nullable();
            $table->boolean('is_faulty')->nullable();
            $table->smallInteger('ds_phase')->nullable();
            $table->smallInteger('is_caste_changed')->nullable();
            $table->smallInteger('effective_yymm')->nullable();
            $table->timestamp('approval_date')->nullable();
            $table->smallInteger('eariler_rejected')->nullable();
            $table->smallInteger('is_aadhar_dup')->nullable();
            $table->smallInteger('is_sent_jb')->nullable();
            $table->smallInteger('is_migrated_jb')->nullable();
            $table->timestamp('real_created_at')->nullable();
            $table->smallInteger('marked_data')->nullable();
            $table->smallInteger('jnmp_marked')->nullable();
            $table->string('jnmp_remarks')->nullable();
            $table->smallInteger('acc_validated_aadhaar_new')->nullable();
            $table->smallInteger('failed_process_type_aadhaar')->nullable();
            $table->string('wbpds_name_as_in_aadhar')->nullable();
            $table->integer('process_acc_validated_aadhar')->nullable();
            $table->smallInteger('acc_validated_aadhar')->nullable();
            $table->smallInteger('next_level_role_id_aadhar_validation')->nullable();
            $table->smallInteger('wrong_dob')->nullable();
            $table->date('jb_dob')->nullable();
            $table->smallInteger('next_level_role_id_dob')->nullable();
            $table->date('new_dob')->nullable();
            $table->string('wbpds_name_as_in_aadhar_sr')->nullable();
            $table->smallInteger('name_is_match_sr')->nullable();
            $table->smallInteger('caste_matched_with_certificate_no')->nullable();
            $table->smallInteger('life_certificate_checked')->nullable();
            $table->smallInteger('life_certificate_pass')->nullable();
            $table->timestamp('life_certificate_lastdatetime')->nullable();
            $table->timestamp('last_biometric')->nullable();
            $table->timestamp('caste_certificate_check_lastdatetime')->nullable();
            $table->smallInteger('caste_certificate_checked')->nullable();
            $table->smallInteger('no_aadhar')->nullable();
            $table->smallInteger('no_aadhar_next_level_role_id')->nullable();
            $table->smallInteger('pre_no_aadhar')->nullable();
            $table->string('life_certificate_msg')->nullable();
            $table->smallInteger('entry_type')->nullable();
            $table->smallInteger('dup_id')->nullable();
            $table->smallInteger('bio_aadhar_checked_api_failed')->nullable();
            $table->string('wbpds_ration_card_no')->nullable();
            $table->string('srs_rcc_category')->nullable();
            $table->string('caste_certificate_validation_message')->nullable();
            $table->smallInteger('wbpds_is_sent')->nullable();
            $table->smallInteger('name_is_match')->nullable();
            $table->smallInteger('aadhaar_no_checked')->nullable();
            $table->timestamp('aadhaar_no_checked_lastdatetime')->nullable();
            $table->smallInteger('aadhaar_no_checked_pass')->nullable();
            $table->text('aadhaar_no_validation_msg')->nullable();
            $table->date('dob_kh')->nullable();
            $table->smallInteger('dob_is_match_kh')->nullable();
            $table->string('wbpds_family_id')->nullable();
            $table->string('srs_name_as_in_aadhar_cur')->nullable();
            $table->date('srs_dob')->nullable();
            $table->string('reactive_reason')->nullable();
            $table->boolean('is_samadhan')->nullable();
            $table->string('old_aadhar_no')->nullable();
            $table->smallInteger('similarity_percentage')->nullable();
            $table->smallInteger('grade')->nullable();
            $table->string('srs_ration_card_no')->nullable();
            $table->string('srs_family_id')->nullable();
            $table->smallInteger('migrated_to_srs')->nullable();
            $table->integer('action_by')->nullable();
            $table->string('action_ip_address')->nullable();
            $table->string('action_type')->nullable();
            $table->smallInteger('payment_suspended')->nullable();
            $table->smallInteger('ds_mark_ix')->nullable();
            $table->date('ds_marking_date')->nullable();
            $table->smallInteger('sm_flag')->nullable();
            $table->smallInteger('cmo_mark')->nullable();
            $table->smallInteger('cmo_entry')->nullable();
            $table->integer('cmo_grievance_id')->nullable();
            $table->smallInteger('ds_phase_mark')->nullable();
           $table->smallInteger('source_type')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lb_scheme.ben_personal_details');
    }
};
