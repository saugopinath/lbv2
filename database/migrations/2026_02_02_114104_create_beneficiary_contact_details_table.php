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
        Schema::create('lb_scheme.beneficiary_contact_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('scheme_id');
            $table->bigInteger('application_id')->unique();
            $table->bigInteger('beneficiary_id')->nullable();
            $table->integer('state')->nullable();
            $table->integer('district_id');
            $table->integer('rural_urban');   // Rural / Urban
            $table->integer('blockurban');  // Block / Municipality
            $table->integer('gpWard');       // GP / Ward
            $table->string('villtowncity', 150)->nullable();
            $table->string('postoffice', 150)->nullable();
            $table->string('policestation', 150)->nullable();
            $table->string('housepremiseno', 150)->nullable();
            $table->string('pincode', 6)->nullable();
            $table->jsonb('other_details')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lb_scheme.beneficiary_contact_details');
    }
};
