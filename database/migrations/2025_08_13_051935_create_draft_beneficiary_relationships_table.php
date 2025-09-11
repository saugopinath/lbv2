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
       Schema::create('pension.draft_beneficiary_relationships', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('application_id');

            $table->Integer('created_by');
            $table->string('full_name');
            $table->smallInteger('relation_type_id');
            $table->foreign('created_by','user_id_fk')->references('id')->on('users');
            $table->foreign('relation_type_id','relation_type_id_fk')->references('id')->on('codemasters');
             $table->foreign('application_id','application_id_fk')->references('application_id')->on('pension.draft_beneficiary_personals')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lb_scheme.draft_beneficiary_relationships');
    }
};