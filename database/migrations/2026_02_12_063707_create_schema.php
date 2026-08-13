<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::connection('pgsql_jblbV2')->statement("CREATE SCHEMA IF NOT EXISTS pension");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::connection('pgsql_jblbV2')->statement("DROP SCHEMA IF EXISTS pension");
    }
};
