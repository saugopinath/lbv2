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
        Schema::create('jnmp.lb_mapping', function (Blueprint $table) {
            $table->id();
            $table->integer('lb_id')->nullable();
            $table->integer('jnm_id')->nullable();
            $table->string('aadhar_hash')->nullable()->unique();
            $table->smallInteger('payment_suspend')->nullable();

            // $table->foreign('aadhar_hash', 'aadhar_hash_fk')->references('aadhar_hash')->on('pension.beneficiary_aadhars')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jnmp.lb_mapping');
    }
};
