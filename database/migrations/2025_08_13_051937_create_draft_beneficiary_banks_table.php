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
        Schema::create('pension.draft_beneficiary_banks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('application_id');


            $table->Integer('created_by');
            $table->string('ifsc',20);
            $table->string('bank_account_number',30)->unique();
            $table->foreign('ifsc','ifsc_fk')->references('code')->on('public.ifsccodemasters');
            $table->foreign('created_by','user_id_fk')->references('id')->on('public.users');
            $table->foreign('application_id','application_id_fk')->references('application_id')->on('pension.draft_beneficiary_personals')->onDelete('cascade');
            $table->timestamps();
            $table->index('application_id','draft_beneficiary_banks_application_id_index');
            $table->index('bank_account_number','draft_beneficiary_banks_bank_account_number_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lb_scheme.draft_beneficiary_banks');
    }
};
