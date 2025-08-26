<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lb_scheme.scheme_validation_parameter_settings', function (Blueprint $table) {
            $table->id(); 

            $table->unsignedBigInteger('scheme_id');
            $table->unsignedBigInteger('master_code');
            $table->unsignedBigInteger('parameter_code');

            $table->boolean('is_active')->default(false);
            $table->integer('min_score')->nullable();
            $table->integer('max_score')->nullable();
            $table->date('from_affected_date');
            $table->date('to_affected_date');

            $table->unique(['master_code', 'parameter_code'], 'uq_master_parameter');
            $table->foreign('master_code')->references('id')->on('codemasters')->onDelete('cascade');
            $table->foreign('parameter_code')->references('id')->on('codemasters')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lb_scheme.scheme_validation_parameter_settings');
    }
};
