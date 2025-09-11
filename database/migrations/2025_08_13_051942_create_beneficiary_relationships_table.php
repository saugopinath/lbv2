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
        Schema::create('pension.beneficiary_relationships', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('application_id');
            $table->unsignedBigInteger('beneficiary_id');
            $table->foreign('beneficiary_id', 'beneficiary_id_fk')->references('beneficiary_id')->on('pension.beneficiary_personals')->onDelete('cascade');

            $table->Integer('created_by');
            $table->string('full_name');
            $table->smallInteger('relation_type_id');
            $table->foreign('created_by', 'user_id_fk')->references('id')->on('users');
            $table->foreign('relation_type_id', 'relation_type_id_fk')->references('id')->on('codemasters');
            $table->timestamps();
            $table->index('beneficiary_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lb_scheme.beneficiary_relationships');
    }
};
