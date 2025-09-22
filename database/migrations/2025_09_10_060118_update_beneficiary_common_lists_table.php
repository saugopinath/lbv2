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
        Schema::table('lb_scheme.beneficiary_common_lists', function (Blueprint $table) {
            $table->unsignedBigInteger('beneficiary_id')->unique();
            $table->string('mobile_no')->nullable();
            $table->text('encoded_aadhar')->nullable();
            $table->string('bank_account_number', 30)->nullable();
            $table->smallInteger('applicant_status')->nullable();
            $table->boolean('is_reject')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lb_scheme.beneficiary_common_lists', function (Blueprint $table) {
            $table->dropColumn([
                'beneficiary_id',
                'mobile_no',
                'encoded_aadhar',
                'bank_account_number',
                'applicant_status',
                'is_reject',
            ]);
        });
    }
};
