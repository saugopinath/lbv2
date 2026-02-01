<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('lb_scheme.pan_id_details', function (Blueprint $table) {

        $table->id();
          $table->unsignedBigInteger('application_id')->unique();
$table->unsignedBigInteger('beneficiary_id')->unique();
                      $table->integer('caste1')->nullable();
            $table->string('caste_number', 20)->nullable();

            $table->timestamps();
          
        $table->foreign('application_id', 'application_id_fk')
                ->references('application_id')
                ->on('lb_scheme.unique_app_ben_ids')
                ->cascadeOnDelete();

        $table->foreign('beneficiary_id', 'beneficiary_id_fk')
                ->references('beneficiary_id')
                ->on('lb_scheme.unique_app_ben_ids')
                ->cascadeOnDelete();
        
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lb_scheme.pan_id_details');
    }
};