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
        Schema::create('pension.beneficiary_enclosures', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('beneficiary_id')->nullable();
            $table->unsignedBigInteger('application_id');
            $table->text('attched_document');
            $table->string('ip_address');
            $table->string('document_extension');
            $table->string('document_mime_type');
            $table->smallInteger('document_type');
            $table->Integer('created_by');
            $table->unique(['application_id', 'document_type']);
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
