<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pension.unique_app_ben_ids', function (Blueprint $table) {
            $table->unsignedBigInteger('application_id')->primary();
            $table->unsignedBigInteger('beneficiary_id')->unique();
            
            $table->timestamps();
        });
        // Create sequence for application_id starting from 500
        DB::statement("CREATE SEQUENCE IF NOT EXISTS pension_unique_app_ben_ids_application_id_seq START 500;");
        DB::statement("
            ALTER TABLE pension.unique_app_ben_ids 
            ALTER COLUMN application_id 
            SET DEFAULT nextval('pension_unique_app_ben_ids_application_id_seq');
        ");

        // Create sequence for beneficiary_id starting from 100
        DB::statement("CREATE SEQUENCE IF NOT EXISTS pension_unique_app_ben_ids_beneficiary_id_seq START 100;");
        DB::statement("
            ALTER TABLE pension.unique_app_ben_ids 
            ALTER COLUMN beneficiary_id 
            SET DEFAULT nextval('pension_unique_app_ben_ids_beneficiary_id_seq');
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pension.unique_app_ben_ids');
        DB::statement("DROP SEQUENCE IF EXISTS pension_unique_app_ben_ids_application_id_seq;");
        DB::statement("DROP SEQUENCE IF EXISTS pension_unique_app_ben_ids_beneficiary_id_seq;");
    }
};