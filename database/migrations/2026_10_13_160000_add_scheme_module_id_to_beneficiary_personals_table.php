<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE pension.beneficiary_personals ADD COLUMN IF NOT EXISTS scheme_module_id bigint");
        DB::statement("COMMENT ON COLUMN pension.beneficiary_personals.scheme_module_id IS 'ID of the dynamic workflow scheme module (dynamic_workflow_scheme_modules.id) under which the application was submitted'");

        // Backfill existing records
        DB::statement("UPDATE pension.beneficiary_personals SET scheme_module_id = 3 WHERE application_id = 1000000000 AND scheme_module_id IS NULL");
        DB::statement("UPDATE pension.beneficiary_personals SET scheme_module_id = 2 WHERE application_id = 1000000001 AND scheme_module_id IS NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE pension.beneficiary_personals DROP COLUMN IF EXISTS scheme_module_id");
    }
};
