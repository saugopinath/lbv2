<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    protected $connection = 'pgsql_jblbV2';
    protected $schema = 'pension';

    public function up(): void
    {
        $conn = DB::connection($this->connection);

        $conn->statement("
            CREATE SCHEMA IF NOT EXISTS {$this->schema}
        ");

        /*
        |--------------------------------------------------------------------------
        | 1. unique_app_ben_ids
        |--------------------------------------------------------------------------
        */
        $conn->statement("
            CREATE TABLE IF NOT EXISTS {$this->schema}.unique_app_ben_ids
            (
                scheme_id integer NOT NULL DEFAULT 0,
                application_id bigint NOT NULL DEFAULT nextval('{$this->schema}.application_id_seq'::regclass),
                beneficiary_id bigint NOT NULL DEFAULT nextval('{$this->schema}.beneficiary_id_seq'::regclass),
                created_at timestamp without time zone,
                updated_at timestamp without time zone
            )
        ");

        /*
        |--------------------------------------------------------------------------
        | 2. beneficiary_personals (Partitioned)
        |--------------------------------------------------------------------------
        */
        $conn->statement("
            CREATE TABLE IF NOT EXISTS {$this->schema}.beneficiary_personals
            (
                scheme_id integer NOT NULL,
                application_id bigint NOT NULL,
                beneficiary_id bigint NOT NULL,
                application_type varchar(100),
                application_date date,
                ds_registration_no varchar(100),
                ds_date date,
                beneficiary_name varchar(250),
                age integer,
                email varchar(250),
                dob date,
                ben_father_name varchar(250),
                ben_mother_name varchar(250),
                mar_statu integer,
                ben_spouse_name varchar(250),
                caste char(10),
                caste_cer_no varchar(250),
                next_level_role_id smallint,
                is_final smallint default 0,
                created_by_dist_code integer,
                created_by_local_body_code integer,
                other_details jsonb,
                created_by integer,
                updated_by integer,
                is_clean smallint NOT NULL DEFAULT 1,
                created_at timestamp without time zone,
                updated_at timestamp without time zone
            ) PARTITION BY LIST (scheme_id)
        ");

        /*
        |--------------------------------------------------------------------------
        | 3. beneficiary_contacts (Partitioned)
        |--------------------------------------------------------------------------
        */
        $conn->statement("
            CREATE TABLE IF NOT EXISTS {$this->schema}.beneficiary_contacts
            (
                scheme_id integer NOT NULL,
                application_id bigint NOT NULL,
                beneficiary_id bigint NOT NULL,
                state varchar(100),
                district_id integer,
                rural_urban smallint,
                blockurban integer,
                gpward integer,
                policestation varchar(100),
                villtowncity varchar(100),
                housepremiseno varchar(100),
                postoffice varchar(100),
                pincode char(6),
                other_details jsonb,
                is_clean smallint NOT NULL DEFAULT 1,
                created_at timestamp without time zone,
                updated_at timestamp without time zone
            ) PARTITION BY LIST (scheme_id)
        ");

        /*
        |--------------------------------------------------------------------------
        | 4. beneficiary_bank (Partitioned)
        |--------------------------------------------------------------------------
        */
        $conn->statement("
            CREATE TABLE IF NOT EXISTS {$this->schema}.beneficiary_banks
            (
                scheme_id integer NOT NULL,
                application_id bigint NOT NULL,
                beneficiary_id bigint NOT NULL,
                ifscode varchar(25),
                bankname varchar(150),
                bank_branch_name varchar(100),
                bankaccountnumber varchar(30),
                other_details jsonb,
                is_clean smallint NOT NULL DEFAULT 1,
                created_at timestamp without time zone,
                updated_at timestamp without time zone
            ) PARTITION BY LIST (scheme_id)
        ");

        /*
        |--------------------------------------------------------------------------
        | 5. beneficiary_aadhar (Partitioned)
        |--------------------------------------------------------------------------
        */
        $conn->statement("
            CREATE TABLE IF NOT EXISTS {$this->schema}.beneficiary_aadhars
            (
                scheme_id integer NOT NULL,
                application_id bigint NOT NULL,
                beneficiary_id bigint NOT NULL,
                encode_key text,
                encoded_aadhar text,
                aadhar_vault text,
                aadhar_hash varchar(255),
                is_clean smallint DEFAULT 1,
                created_at timestamp without time zone,
                updated_at timestamp without time zone
            ) PARTITION BY LIST (scheme_id)

        ");

        $conn->statement("
            CREATE TABLE IF NOT EXISTS {$this->schema}.beneficiary_documents(
            scheme_id integer NULL,
            beneficiary_id BIGINT NULL,
            application_id BIGINT NOT NULL,
            attched_document TEXT NOT NULL,
            ip_address VARCHAR(45) NOT NULL,
            document_extension VARCHAR(50) NOT NULL,
            document_mime_type VARCHAR(150) NOT NULL,
            document_type SMALLINT NOT NULL,
            created_by INTEGER NOT NULL,
            tab_code INTEGER NULL,
            is_clean smallint DEFAULT 1,
            created_at TIMESTAMP WITHOUT TIME ZONE,
            updated_at TIMESTAMP WITHOUT TIME ZONE
        ) PARTITION BY LIST (scheme_id);
        ");
        $conn->statement("
            CREATE TABLE IF NOT EXISTS {$this->schema}.beneficiary_self_declarations(
            scheme_id integer NOT NULL,
            application_id bigint NOT NULL,
            beneficiary_id bigint NOT NULL,
            other_details jsonb,
            is_clean smallint DEFAULT 1,
            created_at timestamp without time zone,
            updated_at timestamp without time zone
        ) PARTITION BY LIST (scheme_id);");
    }

    public function down(): void
    {
        $conn = DB::connection($this->connection);

        $tables = [
            "beneficiary_self_declarations",
            'beneficiary_documents',
            'beneficiary_aadhars',
            'beneficiary_banks',
            'beneficiary_contacts',
            'beneficiary_personals',
            'unique_app_ben_ids',
        ];

        foreach ($tables as $table) {
            $conn->statement("
                DROP TABLE IF EXISTS {$this->schema}.{$table} CASCADE
            ");
        }
    }
};
