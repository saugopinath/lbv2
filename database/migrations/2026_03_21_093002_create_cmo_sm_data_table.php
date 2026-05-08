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

        DB::statement("CREATE SEQUENCE IF NOT EXISTS cmo.cmo_sm_data_id_seq START 1 INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 NO CYCLE");
        Schema::create('cmo.cmo_sm_data', function (Blueprint $table) {
            $table->bigInteger('id')->default(DB::raw("nextval('cmo.cmo_sm_data_id_seq'::regclass)"))->primary();
            $table->text('grievance_id')->nullable(false);
            $table->text('grievance_no');
            $table->string('grievance_source', 50)->nullable();
            $table->string('receipt_mode', 50)->nullable();
            $table->string('received_at', 50)->nullable();
            $table->string('reference_no', 100)->nullable();
            $table->string('applicant_name', 100)->nullable();
            $table->text('pri_cont_no')->nullable();
            $table->string('alt_cont_no', 100)->nullable();
            $table->string('cont_email', 100)->nullable();
            $table->string('applicant_gender', 50)->nullable();
            $table->string('applicant_age', 50)->nullable();
            $table->string('applicant_caste', 50)->nullable();
            $table->string('applicant_reigion', 50)->nullable();
            $table->text('applicant_address')->nullable();
            $table->string('state_id', 50)->nullable();
            $table->string('district_id', 50)->nullable();
            $table->string('block_id', 50)->nullable();
            $table->string('municipality_id', 50)->nullable();
            $table->string('gp_id', 50)->nullable();
            $table->string('ward_id', 50)->nullable();
            $table->string('police_station_id', 50)->nullable();
            $table->string('assembly_const_id', 50)->nullable();
            $table->string('postoffice_id', 50)->nullable();
            $table->string('employment_type', 50)->nullable();
            $table->text('employment_status')->nullable();
            $table->text('grievance_category')->nullable();
            $table->text('grievance_description')->nullable();
            $table->text('action_requested')->nullable();
            $table->text('usb_unique_id')->nullable();
            $table->text('parent_grievance_id')->nullable();
            $table->string('status', 50)->nullable();
            $table->string('atr_recv_cmo_flag', 20)->nullable();
            $table->string('emergency_flag', 20)->nullable();
            $table->text('created_by')->nullable();
            $table->text('updated_by')->nullable();
            $table->text('sub_division_id')->nullable();
            $table->text('uploaded_doc_id')->nullable();
            $table->text('created_by_position')->nullable();
            $table->text('updated_by_position')->nullable();
            $table->text('assigned_to_id')->nullable();
            $table->text('assigned_to_position')->nullable();
            $table->string('educational_qualification_id', 100)->nullable();
            $table->string('professional_qualification_id', 100)->nullable();
            $table->string('skill_id', 100)->nullable();
            $table->text('address_type')->nullable();
            $table->text('action_taken_note')->nullable();
            $table->string('atn_id', 20)->nullable();
            $table->text('force_closure_2020')->nullable();
            $table->string('closure_reason_id', 20)->nullable();
            $table->string('deo_phone_no', 20)->nullable();
            $table->string('assigned_by_office_id', 20)->nullable();
            $table->string('assigned_to_office_id', 20)->nullable();
            $table->string('assigned_by_office_cat', 20)->nullable();
            $table->string('assigned_to_office_cat', 20)->nullable();
            $table->string('atr_submit_by_lastest_office_id', 20)->nullable();
            $table->string('direct_close', 20)->nullable();
            $table->smallInteger('lb_next_level_role_id')->nullable();
            $table->timestamp('marked_date')->nullable();
            $table->integer('marked_by')->nullable();
            $table->text('lb_name')->nullable();
            $table->integer('lb_id')->nullable();
            $table->integer('scheme_id')->nullable();
            $table->smallInteger('is_processed')->default(0);
            $table->text('atr_type')->nullable();
            $table->text('level_type')->nullable();
            $table->text('remarks')->nullable();
            $table->integer('redressed_by')->nullable();
            $table->timestamp('redressed_date')->nullable();
            $table->smallInteger('is_redressed')->default(0);
            $table->smallInteger('lb_rural_urban_id')->nullable();
            $table->smallInteger('is_change_block')->default(0);
            $table->integer('change_block_by')->nullable();
            $table->timestamp('change_block_date')->nullable();
            $table->timestamp('response_back_date')->nullable();
            $table->integer('response_back_by')->nullable();
            $table->timestamp('api_fetching_date')->nullable();
            $table->text('atr_recv_cmo_date')->nullable();
            $table->text('grievence_close_date')->nullable();
            $table->text('created_on')->nullable();
            $table->text('updated_on')->nullable();
            $table->text('grievance_generate_date')->nullable();
            $table->text('current_atr_date')->nullable();
            $table->text('lgd_dist')->nullable();
            $table->text('lgd_block')->nullable();
            $table->text('lgd_muni')->nullable();
            $table->integer('lb_application_id')->nullable();
            $table->string('atr_desc', 200)->nullable();
            $table->smallInteger('is_mark')->default(0);
            $table->smallInteger('send_to_op')->default(0);
            $table->integer('send_to_op_by')->nullable();
            $table->timestamp('send_to_op_date')->nullable();
            $table->smallInteger('table_source')->nullable();
            $table->string('lb_dist_code')->nullable();
            $table->string('lb_local_body_code')->nullable();
            $table->string('lb_gp_ward_code')->nullable();
            $table->string('redressed_status')->nullable();
            $table->jsonb('old_data')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cmo.cmo_sm_data');
        DB::statement("DROP SEQUENCE IF EXISTS cmo.cmo_sm_data_id_seq");
    }
};
