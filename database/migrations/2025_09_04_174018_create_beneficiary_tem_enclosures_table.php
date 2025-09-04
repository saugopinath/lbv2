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
        Schema::create('beneficiary_tem_enclosures', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedInteger('application_id');
            $table->text('attched_document');
            $table->string('ip_address');
            $table->string('document_extension');
            $table->string('document_mime_type');
            $table->smallInteger('document_type');
            $table->Integer('created_by');
            $table->unique(['application_id', 'document_type']);
            $table->index('application_id','beneficiary_tem_enclosures_application_id_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beneficiary_tem_enclosures');
    }
};
