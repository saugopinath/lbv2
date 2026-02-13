<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {

    protected $connection = 'pgsql_jblbV2';
    protected $schema = 'pension';
    protected $schemeIds = [1, 2, 3, 5, 6, 7, 8, 9, 10, 11, 13, 17, 19, 20];
    protected $isCleans = [1, 2, 10];

    public function up(): void
    {
        $conn = DB::connection($this->connection);

        foreach ($this->schemeIds as $schemeId) {

            /*
            |--------------------------------------------------------------------------
            | 1️⃣ beneficiary_personal
            |--------------------------------------------------------------------------
            */

            $conn->statement("
                CREATE TABLE IF NOT EXISTS {$this->schema}.beneficiary_personals_{$schemeId}
                PARTITION OF {$this->schema}.beneficiary_personals
                FOR VALUES IN ({$schemeId})
                PARTITION BY LIST (is_clean)
            ");

            foreach ($this->isCleans as $isClean) {
                $conn->statement("
                    CREATE TABLE IF NOT EXISTS {$this->schema}.beneficiary_personals_{$schemeId}_{$isClean}
                    PARTITION OF {$this->schema}.beneficiary_personals_{$schemeId}
                    FOR VALUES IN ({$isClean})
                ");
            }

            /*
            |--------------------------------------------------------------------------
            | 2️⃣ beneficiary_contact
            |--------------------------------------------------------------------------
            */

            $conn->statement("
                CREATE TABLE IF NOT EXISTS {$this->schema}.beneficiary_contacts_{$schemeId}
                PARTITION OF {$this->schema}.beneficiary_contacts
                FOR VALUES IN ({$schemeId})
                PARTITION BY LIST (is_clean)
            ");

            foreach ($this->isCleans as $isClean) {
                $conn->statement("
                    CREATE TABLE IF NOT EXISTS {$this->schema}.beneficiary_contacts_{$schemeId}_{$isClean}
                    PARTITION OF {$this->schema}.beneficiary_contacts_{$schemeId}
                    FOR VALUES IN ({$isClean})
                ");
            }

            /*
            |--------------------------------------------------------------------------
            | 3️⃣ beneficiary_bank
            |--------------------------------------------------------------------------
            */

            $conn->statement("
                CREATE TABLE IF NOT EXISTS {$this->schema}.beneficiary_banks_{$schemeId}
                PARTITION OF {$this->schema}.beneficiary_banks
                FOR VALUES IN ({$schemeId})
                PARTITION BY LIST (is_clean)
            ");

            foreach ($this->isCleans as $isClean) {
                $conn->statement("
                    CREATE TABLE IF NOT EXISTS {$this->schema}.beneficiary_banks_{$schemeId}_{$isClean}
                    PARTITION OF {$this->schema}.beneficiary_banks_{$schemeId}
                    FOR VALUES IN ({$isClean})
                ");
            }

            /*
            |--------------------------------------------------------------------------
            | 4️⃣ beneficiary_aadhar
            |--------------------------------------------------------------------------
            */

            $conn->statement("
                CREATE TABLE IF NOT EXISTS {$this->schema}.beneficiary_aadhars_{$schemeId}
                PARTITION OF {$this->schema}.beneficiary_aadhars
                FOR VALUES IN ({$schemeId})
                PARTITION BY LIST (is_clean)
            ");

            foreach ($this->isCleans as $isClean) {
                $conn->statement("
                    CREATE TABLE IF NOT EXISTS {$this->schema}.beneficiary_aadhars_{$schemeId}_{$isClean}
                    PARTITION OF {$this->schema}.beneficiary_aadhars_{$schemeId}
                    FOR VALUES IN ({$isClean})
                ");
            }
            $conn->statement("
                CREATE TABLE IF NOT EXISTS {$this->schema}.beneficiary_documents_{$schemeId}
                PARTITION OF {$this->schema}.beneficiary_documents
                FOR VALUES IN ({$schemeId})
                PARTITION BY LIST (is_clean)
            ");

            foreach ($this->isCleans as $isClean) {
                $conn->statement("
                    CREATE TABLE IF NOT EXISTS {$this->schema}.beneficiary_documents_{$schemeId}_{$isClean}
                    PARTITION OF {$this->schema}.beneficiary_documents_{$schemeId}
                    FOR VALUES IN ({$isClean})
                ");
            }
            /*
            |--------------------------------------------------------------------------
            | 5️⃣ beneficiary_self_declarations
            |--------------------------------------------------------------------------
            */

            $conn->statement("
                CREATE TABLE IF NOT EXISTS {$this->schema}.beneficiary_self_declarations_{$schemeId}
                PARTITION OF {$this->schema}.beneficiary_self_declarations
                FOR VALUES IN ({$schemeId})
                PARTITION BY LIST (is_clean)
            ");

            foreach ($this->isCleans as $isClean) {
                $conn->statement("
                    CREATE TABLE IF NOT EXISTS {$this->schema}.beneficiary_self_declarations_{$schemeId}_{$isClean}
                    PARTITION OF {$this->schema}.beneficiary_self_declarations_{$schemeId}
                    FOR VALUES IN ({$isClean})
                ");
            }
        }
    }

    public function down(): void
    {
        $conn = DB::connection($this->connection);

        foreach ($this->schemeIds as $schemeId) {
            foreach ($this->isCleans as $isClean) {

                $conn->statement("DROP TABLE IF EXISTS {$this->schema}.beneficiary_personals_{$schemeId}_{$isClean} CASCADE");
                $conn->statement("DROP TABLE IF EXISTS {$this->schema}.beneficiary_contacts_{$schemeId}_{$isClean} CASCADE");
                $conn->statement("DROP TABLE IF EXISTS {$this->schema}.beneficiary_banks_{$schemeId}_{$isClean} CASCADE");
                $conn->statement("DROP TABLE IF EXISTS {$this->schema}.beneficiary_aadhars_{$schemeId}_{$isClean} CASCADE");
                $conn->statement("DROP TABLE IF EXISTS {$this->schema}.beneficiary_documents_{$schemeId}_{$isClean} CASCADE");
                $conn->statement("DROP TABLE IF EXISTS {$this->schema}.beneficiary_self_declarations_{$schemeId}_{$isClean} CASCADE");
            }

            $conn->statement("DROP TABLE IF EXISTS {$this->schema}.beneficiary_personals_{$schemeId} CASCADE");
            $conn->statement("DROP TABLE IF EXISTS {$this->schema}.beneficiary_contacts_{$schemeId} CASCADE");
            $conn->statement("DROP TABLE IF EXISTS {$this->schema}.beneficiary_banks_{$schemeId} CASCADE");
            $conn->statement("DROP TABLE IF EXISTS {$this->schema}.beneficiary_aadhars_{$schemeId} CASCADE");
            $conn->statement("DROP TABLE IF EXISTS {$this->schema}.beneficiary_documents_{$schemeId} CASCADE");
            $conn->statement("DROP TABLE IF EXISTS {$this->schema}.beneficiary_self_declarations_{$schemeId} CASCADE");
        }
    }
};
