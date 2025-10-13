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
        Schema::create('applicant_incomplet_deatils', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('application_id');
            $table->unsignedBigInteger('beneficiary_id')->nullable();
            $table->unsignedBigInteger('incomplet_type');
            $table->smallInteger('next_level_request_id')->nullable();
            $table->json('new_value')->nullable();
            $table->json('old_value')->nullable();
            $table->unsignedBigInteger('request_id')->nullable();
            $table->smallInteger('is_active')->default(1);
            $table->smallInteger('change_type')->nullable();
            $table->foreign('application_id', 'application_id_fk')
                ->references('sourceable_id')
                ->on('lb_scheme.beneficiary_common_lists')
                ->onDelete('cascade');
            $table->foreign('request_id', 'request_id_fk')
                ->references('id')
                ->on('accept_reject_infos')
                ->onDelete('cascade');
            $table->foreign('incomplet_type')
                ->references('incomplet_type_code')
                ->on('incomplet_type_model_mappings')
                ->onDelete('cascade');
            $table->timestamps();
            $table->index('application_id');
            $table->index('beneficiary_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicant_incomplet_deatils');
    }
};
