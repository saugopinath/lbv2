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
        Schema::create('pension.beneficiary_declarations', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('beneficiary_id');
            $table->foreign('beneficiary_id', 'beneficiary_id_fk')->references('beneficiary_id')->on('pension.beneficiary_personals')->onDelete('cascade');
            $table->unsignedBigInteger('application_id');
            $table->Integer('created_by');
            $table->boolean('is_resident');
            $table->boolean('earn_monthly_remuneration');
            $table->boolean('info_genuine_decl');
            $table->boolean('av_status');
            // $table->smallInteger('identification_type_id');
            $table->foreign('created_by','user_id_fk')->references('id')->on('users');

            $table->timestamps();
            $table->index('application_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lb_scheme.beneficiary_declarations');
    }
};
