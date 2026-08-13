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
        Schema::create('pension.beneficiary_tem_enclosures', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedInteger('application_id');
            $table->unsignedInteger('beneficiary_id');
            $table->unsignedInteger('scheme_id');
            $table->text('attched_document');
            $table->string('ip_address');
            $table->string('document_extension');
            $table->string('document_mime_type');
            $table->smallInteger('document_type');
            $table->Integer('created_by');
            $table->unique(['application_id', 'document_type']);
            $table->index('application_id', 'beneficiary_tem_enclosures_application_id_index');
            $table->index('beneficiary_id', 'beneficiary_tem_enclosures_beneficiary_id_index');
            $table->index('scheme_id', 'beneficiary_tem_enclosures_scheme_id_index');
            $table->index('document_type', 'beneficiary_tem_enclosures_document_type_index');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('pension.beneficiary_tem_enclosures');
    }
};
