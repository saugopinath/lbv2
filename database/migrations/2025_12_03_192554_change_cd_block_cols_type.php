<?php

// database/migrations/2025_12_04_change_cd_block_cols_type.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        // if you use schemas, include schema name
        DB::statement('ALTER TABLE lb_scheme.beneficiary_common_lists ALTER COLUMN cd_block_muni_id TYPE integer USING cd_block_muni_id::integer');
        DB::statement('ALTER TABLE lb_scheme.beneficiary_common_lists ALTER COLUMN cd_gp_ward_id TYPE integer USING cd_gp_ward_id::integer');
    }

    public function down()
    {
        // revert to smallint only if you are sure values fit; otherwise skip down or set to safe fallback
        DB::statement('ALTER TABLE lb_scheme.beneficiary_common_lists ALTER COLUMN cd_block_muni_id TYPE smallint USING cd_block_muni_id::smallint');
        DB::statement('ALTER TABLE lb_scheme.beneficiary_common_lists ALTER COLUMN cd_gp_ward_id TYPE smallint USING cd_gp_ward_id::smallint');
    }
};
