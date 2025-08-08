<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use League\CommonMark\Extension\CommonMark\Node\Inline\Code;
use App\Models\Codemaster;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('office_masters', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address')->nullable();
            $table->string('zip')->nullable();;
            $table->timestamps();
            $table->smallInteger('office_type');
            $table->smallInteger('state_id');
            $table->smallInteger('district_id')->nullable();
            $table->smallInteger('block_id')->nullable();
            $table->Integer('subdivisions_id')->nullable();
            $table->Integer('municipalitiy_id')->nullable();
            $table->Integer('ward_id')->nullable();
            $table->Integer('panchayat_id')->nullable();
            $table->foreign('office_type', 'office_type_fk')->references('id')->on('codemasters');
            $table->foreign('state_id','state_id_fk')->references('id')->on('states');
            $table->foreign('district_id','district_id_fk')->references('id')->on('districts');
            $table->foreign('subdivisions_id','subdivisions_id_fk')->references('id')->on('subdivisions');
            $table->foreign('municipalitiy_id','municipalitiy_id_fk')->references('id')->on('municipalities');
            $table->foreign('ward_id','ward_id_fk')->references('id')->on('wards');
            $table->foreign('block_id','block_id_fk')->references('id')->on('blocks');
            $table->foreign('panchayat_id','panchayat_id_fk')->references('id')->on('panchayats');
            $table->smallInteger('is_active')->default(1);
            $table->index('id');
            $table->index('district_id');
            $table->index('subdivisions_id');
            $table->index('municipalitiy_id');
            $table->index('ward_id');
            $table->index('block_id');
            $table->index('panchayat_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('office_masters');
    }
};
