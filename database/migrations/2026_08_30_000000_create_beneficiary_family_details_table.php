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
        Schema::create('pension.beneficiary_family_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('scheme_id');
            $table->bigInteger('application_id')->unique();
            $table->bigInteger('beneficiary_id')->nullable();
            
            $table->jsonb('other_details')->nullable();
            $table->timestamps();

            $table->foreign('application_id', 'family_application_id_fk')
                  ->references('application_id')
                  ->on('pension.unique_app_ben_ids')
                  ->cascadeOnDelete();

            $table->foreign('scheme_id', 'family_scheme_id_fk')
                  ->references('id')
                  ->on('public.schemes')
                  ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pension.beneficiary_family_details');
    }
};
