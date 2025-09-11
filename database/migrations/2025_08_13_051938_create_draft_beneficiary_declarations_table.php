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
        Schema::create('pension.draft_beneficiary_declarations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('application_id');

            $table->Integer('created_by');
            $table->boolean('is_resident');
            $table->boolean('earn_monthly_remuneration');
            $table->boolean('info_genuine_decl');
            $table->boolean('av_status');
            // $table->smallInteger('identification_type_id');//create foregn key later
            $table->foreign('created_by','user_id_fk')->references('id')->on('public.users');
            $table->foreign('application_id','application_id_fk')->references('application_id')->on('pension.draft_beneficiary_personals')->onDelete('cascade');
            $table->timestamps();
            $table->index('application_id','draft_beneficiary_declarations_application_id_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lb_scheme.draft_beneficiary_declarations');
    }
};
