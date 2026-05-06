<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'pgsql';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::connection($this->connection)->statement(
            'ALTER TABLE pension.beneficiary_documents ADD COLUMN id BIGSERIAL'
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::connection($this->connection)->statement(
            'ALTER TABLE pension.beneficiary_documents DROP COLUMN IF EXISTS id'
        );
    }
};
