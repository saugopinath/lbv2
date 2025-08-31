<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lb_scheme.applicant_reject_revert_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('application_id');
            $table->Integer('created_by');
            $table->smallInteger('reject_revert_reason_id');
            $table->string('remark', 500)->nullable();
            $table->enum('action_type', ['R', 'T'])->default('T')->comment('R: Reject, T: Revert');
            $table->foreign('application_id', 'application_id_fk')->references('sourceable_id')
                ->on('lb_scheme.beneficiary_common_lists')->onDelete('cascade');
            $table->foreign('created_by', 'user_id_fk')->references('id')->on('users');
            $table->foreign('reject_revert_reason_id', 'reject_revert_reason_id_fk')
                ->references('id')
                ->on('public.codemasters');
            $table->index('application_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lb_scheme.applicant_reject_revert_details');
    }
};
