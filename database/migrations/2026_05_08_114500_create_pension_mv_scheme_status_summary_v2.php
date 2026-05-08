<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Creates the materialized view: pension.mv_scheme_status_summary
 *
 * This MV summarises beneficiary workflow-stage counts per scheme.
 * It replaces the legacy home.mv_scheme_status_summary which referenced
 * deprecated tables (pension.beneficiaries, m_scheme, m_scheme_step_rank).
 *
 * Current table mapping:
 *  - pension.beneficiary_personals  → beneficiary records (uses next_level_role_id)
 *  - public.schemes                 → scheme master (id, name, is_active)
 *  - public.workflowstep_rolemappings → workflow steps (scheme_id, rank, role_id,
 *                                       next_label_role_id, is_final_step)
 *
 * Status classification logic:
 *  Each beneficiary's next_level_role_id is matched against the
 *  next_label_role_id of each workflow rank to determine the stage:
 *    rank 1 → entry     (created, not yet forwarded)
 *    rank 2 → verified
 *    rank 3 → approved  (is_final_step = true)
 *    rank 4 → recommended (schemes with 4 steps)
 *    < 0    → rejected
 *
 * The MV is created on the pgsql_app_read connection's pension schema.
 * The index allows CONCURRENT refresh without locking.
 */
return new class extends Migration {
    /**
     * The database connection to use.
     * Ensures DDL runs on the correct PostgreSQL instance.
     */
    protected $connection = 'pgsql_app_read';

    public function up(): void
    {
        // ── 1. Ensure the pension schema exists ────────────────────────────
        DB::connection($this->connection)->statement('CREATE SCHEMA IF NOT EXISTS pension');

        // ── 2. Drop existing MV if it exists (clean slate) ───────────────
        DB::connection($this->connection)->statement(
            'DROP MATERIALIZED VIEW IF EXISTS pension.mv_scheme_status_summary_v2'
        );

        // ── 3. Create the materialized view ─────────────────────────────────────────
        DB::connection($this->connection)->statement("
            CREATE MATERIALIZED VIEW IF NOT EXISTS pension.mv_scheme_status_summary_v2
            TABLESPACE pg_default
            AS
             WITH step_map AS (
                     SELECT workflowstep_rolemappings.scheme_id,
                        workflowstep_rolemappings.rank,
                        min(workflowstep_rolemappings.next_label_role_id) AS step_role_id
                       FROM workflowstep_rolemappings
                      WHERE workflowstep_rolemappings.module_id IS NULL
                      GROUP BY workflowstep_rolemappings.scheme_id, workflowstep_rolemappings.rank
                    ), step_pivot AS (
                     SELECT step_map.scheme_id,
                        min(step_map.step_role_id) FILTER (WHERE step_map.rank = 1) AS entry_role,
                        min(step_map.step_role_id) FILTER (WHERE step_map.rank = 2) AS verify_role,
                        min(step_map.step_role_id) FILTER (WHERE step_map.rank = 3) AS approve_role,
                        min(step_map.step_role_id) FILTER (WHERE step_map.rank = 4) AS recommend_role
                       FROM step_map
                      GROUP BY step_map.scheme_id
                    ), scheme_info AS (
                     SELECT schemes.id AS scheme_id,
                        schemes.name AS scheme_name
                       FROM schemes
                      WHERE schemes.is_active = 1 AND schemes.id IN (20)
                    ), base AS (
                     SELECT b.scheme_id,
                        si.scheme_name,
                            CASE
                                WHEN b.next_level_role_id < 0 THEN 'rejected'::text
                                WHEN b.next_level_role_id = sp.entry_role THEN 'entry'::text
                                WHEN b.next_level_role_id = sp.verify_role THEN 'verified'::text
                                WHEN b.next_level_role_id = sp.approve_role THEN 'approved'::text
                                WHEN b.next_level_role_id = sp.recommend_role AND sp.recommend_role IS NOT NULL THEN 'recomended'::text
                                ELSE 'unknown'::text
                            END AS status
                       FROM pension.beneficiary_personals b
                         LEFT JOIN scheme_info si ON si.scheme_id = b.scheme_id
                         LEFT JOIN step_pivot sp ON sp.scheme_id = b.scheme_id
                      WHERE b.next_level_role_id >= 0
                    )
             SELECT base.scheme_id,
                base.scheme_name,
                count(*) FILTER (WHERE base.status = 'entry'::text) AS entry_count,
                count(*) FILTER (WHERE base.status = 'verified'::text) AS verified_count,
                count(*) FILTER (WHERE base.status = 'approved'::text) AS approved_count,
                count(*) FILTER (WHERE base.status = 'recomended'::text) AS recomended_count,
                count(*) FILTER (WHERE base.status = 'rejected'::text) AS rejected_count
               FROM base
              GROUP BY base.scheme_id, base.scheme_name
            WITH DATA
        ");

        // ── 4. Set ownership ───────────────────────────────────────────────────
        DB::connection($this->connection)->statement(
            'ALTER TABLE IF EXISTS pension.mv_scheme_status_summary_v2 OWNER TO postgres'
        );

        // ── 5. Unique index (required for CONCURRENT refresh) ─────────────────────
        DB::connection($this->connection)->statement("
            CREATE UNIQUE INDEX IF NOT EXISTS idx_mv_scheme_status_summary_v2_scheme_id
                ON pension.mv_scheme_status_summary_v2 USING btree (scheme_id)
                TABLESPACE pg_default
        ");
    }

    public function down(): void
    {
        DB::connection($this->connection)->statement(
            'DROP MATERIALIZED VIEW IF EXISTS pension.mv_scheme_status_summary_v2'
        );
    }
};
