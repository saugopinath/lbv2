<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scheme_validation_parameter_settings', function (Blueprint $table) {
            $table->id(); 

            $table->smallInteger('scheme_id');
            $table->smallInteger('parameter_code');
            $table->smallInteger('master_code');
            $table->boolean('is_active')->default(true);
            $table->integer('min_score')->nullable();
            $table->integer('max_score')->nullable();
            $table->date('from_affected_date');
            $table->date('to_affected_date');

            $table->unique(['master_code', 'parameter_code'], 'uq_master_parameter');
            $table->foreign('scheme_id','scheme_id_fk')->references('id')->on('schemes')->onDelete('cascade');
            $table->foreign('master_code','master_code_fk')->references('id')->on('codemasters')->onDelete('cascade');
            $table->foreign('parameter_code','parameter_code_fk')->references('id')->on('codemasters')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scheme_validation_parameter_settings');
    }
};
