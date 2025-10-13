<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('failed_payment_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('dist_code')->nullable();
            $table->integer('local_body_code')->nullable();
            $table->integer('lot_no')->nullable();
            $table->integer('ben_id')->nullable();
            $table->string('status_code')->nullable();
            $table->text('remarks')->nullable();
            $table->string('ifsc', 20)->nullable();
            $table->string('accno')->nullable();
            $table->smallInteger('pmt_mode')->nullable();
            $table->smallInteger('failed_type')->nullable();
            $table->smallInteger('edited_status')->nullable();
            $table->timestampsTz(); // created_at & updated_at with timezone
            $table->smallInteger('is_migrated')->nullable();
            $table->smallInteger('lot_month')->nullable();
            $table->char('name_status', 1)->nullable();
            $table->string('name_status_code')->nullable();
            $table->string('name_response')->nullable();
            $table->smallInteger('fp_ds_phase')->nullable();
            $table->string('fin_year')->nullable();
            $table->char('mobile_no', 15)->nullable();
            $table->integer('application_id')->nullable();
            $table->boolean('is_sms_send')->default(false);
            $table->boolean('legacy_validation_failed')->default(false);
            $table->string('ben_name')->nullable();
            $table->smallInteger('matching_score')->nullable();
            $table->smallInteger('is_previous_approved')->nullable();
            $table->smallInteger('failed_process_type')->nullable();
            $table->timestamp('visiting_time')->nullable();
            $table->timestamp('visiting_mark_date')->nullable();
            $table->smallInteger('process_complete')->nullable();
            $table->text('tagging_time')->nullable();
            $table->smallInteger('is_minor_mismatch')->nullable();
            $table->char('lot_type', 1)->nullable();
            $table->json('updated_details')->nullable();
            $table->smallInteger('approve_edited_status')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('failed_payment_details');
    }
};
