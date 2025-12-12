<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lb_scheme.update_ben_details', function (Blueprint $table) {
            $table->id();

            $table->integer('failed_tbl_id')->nullable();
            $table->integer('beneficiary_id')->nullable();

            $table->json('old_data')->nullable();
            $table->json('new_data')->nullable();

            $table->integer('user_id')->nullable();

            $table->softDeletes(); // deleted_at

            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->string('remarks')->nullable();

            $table->integer('update_code')->nullable();
            $table->integer('next_level_role_id')->nullable();
            $table->integer('dist_code')->nullable();
            $table->integer('local_body_code')->nullable();

            $table->smallInteger('rural_urban_id')->nullable();
            $table->integer('block_ulb_code')->nullable();
            $table->integer('gp_ward_code')->nullable();

            $table->smallInteger('pmt_mode')->nullable();
            $table->smallInteger('failed_type')->nullable();

            $table->integer('application_id')->nullable();
            $table->bigInteger('ticket_id')->nullable();

            $table->string('ip_address')->nullable();
            $table->string('name_resposne_from_bank')->nullable();
            $table->string('ben_name')->nullable();

            $table->boolean('legacy_validation_update')->nullable();

            $table->string('approved_remarks')->nullable();
            $table->string('reactive_reason')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lb_scheme.update_ben_details');
    }
};
