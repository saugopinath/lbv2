<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {

    private string $schema = 'pension';

    private array $sequences = [
        'application_id_seq' => 1000000000,
        'beneficiary_id_seq' => 2000000000,
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->sequences as $sequence => $startValue) {

            DB::connection('pgsql_jblbV2')->statement("
                CREATE SEQUENCE IF NOT EXISTS {$this->schema}.{$sequence}
                INCREMENT 1
                START {$startValue}
                MINVALUE 1
                MAXVALUE 9223372036854775807
                CACHE 1
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (array_keys($this->sequences) as $sequence) {

            DB::connection('pgsql_jblbV2')->statement("
                DROP SEQUENCE IF EXISTS {$this->schema}.{$sequence}
            ");
        }
    }
};
