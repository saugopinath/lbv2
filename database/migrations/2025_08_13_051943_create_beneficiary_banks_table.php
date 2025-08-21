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
        Schema::create('lb_scheme.beneficiary_banks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('beneficiary_id');

            $table->unsignedInteger('application_id');
            $table->Integer('created_by');
            $table->char('ifsc',11);
            $table->char('bank_account_number',20)->unique();
            $table->foreign('ifsc','ifsc_fk')->references('code')->on('ifsccodemasters');
            $table->foreign('created_by','user_id_fk')->references('id')->on('users');
            $table->foreign('beneficiary_id', 'beneficiary_id_fk')->references('beneficiary_id')->on('lb_scheme.beneficiary_personals')->onDelete('cascade');
            $table->timestamps();
            $table->index('application_id');
            $table->index('bank_account_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lb_scheme.beneficiary_banks');
    }
};
