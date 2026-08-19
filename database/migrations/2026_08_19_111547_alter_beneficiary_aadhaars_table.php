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
        Schema::table('pension.beneficiary_aadhaars', function (Blueprint $table) {
            // Drop unnecessary columns
            $table->dropColumn(['encoded_aadhaar', 'aadhaar_vault', 'aadhaar_hash']);

            // Add the new aadhaar_token column after existing fields
            $table->string('aadhaar_token', 255)
                ->after('encode_key') // Does not work on pgsql
                ->comment('Stores the secure tokenized representation of the Aadhaar number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pension.beneficiary_aadhaars', function (Blueprint $table) {
            // Re-add the removed columns if rolling back
            $table->text('encoded_aadhaar');
            $table->text('aadhaar_vault');
            $table->string('aadhaar_hash');

            // Remove the newly added column
            $table->dropColumn('aadhaar_token');
        });
    }
};
