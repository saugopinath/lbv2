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
        Schema::create('lb_scheme.beneficiary_bank_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('scheme_id');
            $table->bigInteger('application_id')->unique();
            $table->bigInteger('beneficiary_id')->nullable();
            $table->string('bankname', 255)->nullable();
            $table->string('bank_branch_name', 255)->nullable();
            $table->string('bankaccountnumber', 50)->nullable();
            $table->string('ifscode', 11)->nullable();
            $table->jsonb('other_details')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lb_scheme.beneficiary_bank_details');
    }
};
