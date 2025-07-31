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
        Schema::create('lb_scheme.beneficiary_enclosures', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('beneficiary_id');

            $table->unsignedInteger('application_id');
            $table->text('attched_document');
            $table->string('ip_address');
            $table->string('document_extension');
            $table->string('document_mime_type');
            
            $table->smallInteger('document_type');
            $table->Integer('created_by');
            $table->foreign('created_by','user_id_fk')->references('id')->on('public.users');
             $table->foreign('document_type','document_type_id_fk')->references('id')->on('codemasters');
            $table->foreign('beneficiary_id', 'beneficiary_id_fk')->references('beneficiary_id')->on('lb_scheme.beneficiary_personals')->onDelete('cascade');
            $table->index('application_id','beneficiary_enclosures_application_id_index');


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lb_scheme.beneficiary_enclosures');
    }
};
