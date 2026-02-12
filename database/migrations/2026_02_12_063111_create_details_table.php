<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('lb_scheme.details', function (Blueprint $table) {

        $table->id();
        $table->unsignedBigInteger('scheme_id');
          $table->unsignedBigInteger('application_id')->unique();
$table->unsignedBigInteger('beneficiary_id')->unique();
                      $table->string('new_neme', 50)->nullable();

        $table->jsonb('other_details')->nullable();
        $table->timestamps();
          

        $table->foreign('application_id', 'application_id_fk')
                ->references('application_id')
                ->on('lb_scheme.unique_app_ben_ids')
                ->cascadeOnDelete();

        $table->foreign('beneficiary_id', 'beneficiary_id_fk')
                ->references('beneficiary_id')
                ->on('lb_scheme.unique_app_ben_ids')
                ->cascadeOnDelete();
        $table->foreign('scheme_id', 'scheme_id_fk')
                ->references('id')
                ->on('public.schemes')
                ->cascadeOnDelete();
        
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lb_scheme.details');
    }
};