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
        Schema::create('lb_scheme.beneficiary_approved_lists', function (Blueprint $table) {
            $table->unsignedInteger('application_id');
            $table->smallInteger('district_id');
            $table->smallInteger('block_id')->nullable();
            $table->mediumInteger('sub_division_id')->nullable();
            $table->integer('municipality_id')->nullable();
            $table->integer('ward_id')->nullable();
            $table->integer('panchayat_id')->nullable();
            $table->morphs('source');
            $table->foreign('application_id', 'beneficiary_id_fk')->references('application_id')->on('lb_scheme.beneficiary_personals')->onDelete('cascade');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lb_scheme.beneficiary_approved_lists');
    }
};
