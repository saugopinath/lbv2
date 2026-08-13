<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lb_scheme.beneficiary_aadhaars', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('application_id');
            $table->unsignedBigInteger('beneficiary_id')->nullable();
            $table->integer('scheme_id');
            $table->Integer('created_by');
            $table->string('encode_key')->nullable();
            $table->text('encoded_aadhaar');
            $table->text('aadhaar_vault');
            $table->string('aadhaar_hash')->nullable()->unique();
            $table->foreign('created_by', 'user_id_fk')->references('id')->on('users');
            $table->foreign('application_id', 'application_id_fk')->references('application_id')->on('lb_scheme.unique_app_ben_ids');
            $table->foreign('beneficiary_id', 'beneficiary_id_fk')->references('beneficiary_id')->on('lb_scheme.unique_app_ben_ids')->onDelete('cascade');
            $table->timestamps();
            $table->index('aadhaar_hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lb_scheme.beneficiary_aadhaars');
    }
};
