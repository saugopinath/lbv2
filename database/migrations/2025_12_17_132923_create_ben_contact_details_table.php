<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lb_scheme.ben_contact_details', function (Blueprint $table) {

            $table->integer('application_id')->primary();
            $table->integer('beneficiary_id')->nullable();

            $table->integer('dist_code')->nullable();
            $table->char('police_station')->nullable();
            $table->smallInteger('rural_urban_id')->nullable();

            $table->integer('block_ulb_code')->nullable();
            $table->string('block_ulb_name')->nullable();
            $table->char('block_ulb_type')->nullable();

            $table->integer('gp_ward_code')->nullable();
            $table->string('gp_ward_name')->nullable();

            $table->string('village_town_city')->nullable();
            $table->string('house_premise_no')->nullable();
            $table->string('post_office')->nullable();

            $table->decimal('pincode', 6, 0)->nullable();
            $table->integer('residency_period')->nullable();

            $table->string('created_by_level')->nullable();

            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->integer('created_by')->nullable();
            $table->string('ip_address')->nullable();

            $table->integer('created_by_dist_code')->nullable();
            $table->integer('created_by_local_body_code')->nullable();

            $table->smallInteger('jnmp_marked')->nullable();
            $table->smallInteger('ds_phase')->nullable();

            $table->integer('sr_dist_code')->nullable();
            $table->integer('sr_block_ulb_code')->nullable();
            $table->string('sr_block_ulb_name')->nullable();

            $table->integer('sr_gp_ward_code')->nullable();
            $table->string('sr_gp_ward_name')->nullable();

            $table->integer('action_by')->nullable();
            $table->string('action_ip_address')->nullable();
            $table->string('action_type')->nullable();
           $table->smallInteger('source_type')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lb_scheme.ben_contact_details');
    }
};
