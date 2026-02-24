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
        Schema::create('pension.caste_modification_infos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('application_id');
            $table->unsignedBigInteger('beneficiary_id');
            $table->integer('scheme_id');
            $table->jsonb('old_data')->nullable();
            $table->jsonb('new_data')->nullable();
            $table->smallInteger('caste_request_type');
            $table->smallInteger('next_level_requested_id');
            $table->unsignedBigInteger('request_id')->nullable();
            $table->boolean('is_active')->default(1);
            $table->unsignedInteger('created_by');
            $table->unsignedInteger('updated_by')->nullable();
            // $table->Integer('created_by_dist_code');
            // $table->Integer('created_by_local_body_code');
            $table->timestamps();
            // $table->foreign('application_id', 'application_id_fk')->references('application_id')
            //     ->on('pension.beneficiary_personals')->onDelete('cascade');
            // $table->foreign('beneficiary_id', 'beneficiary_id_fk')->references('beneficiary_id')
            //     ->on('pension.beneficiary_personals')->onDelete('cascade');
            $table->foreign('next_level_requested_id', 'next_level_requested_id_fk')->references('id')
                ->on('codemasters')->onDelete('cascade');
            $table->foreign('created_by', 'caste_modification_infos_created_by_fk')->references('id')
                ->on('users')->onDelete('cascade');
            $table->foreign('request_id', 'request_id_fk')
                ->references('id')
                ->on('accept_reject_infos')
                ->onDelete('cascade');
            $table->index('application_id');
            $table->index('beneficiary_id');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('pension.caste_modification_infos');
    }
};
