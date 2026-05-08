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
            'DROP MATERIALIZED VIEW IF EXISTS pension.mv_scheme_status_summary_v1'
        );

        // ── 3. Create the updated materialized view ───────────────────────
        DB::connection($this->connection)->statement("
            CREATE MATERIALIZED VIEW pension.mv_scheme_status_summary_v1 AS

            WITH

            -- Step-rank map: for each scheme, collect the next_label_role_id
            -- per workflow rank (1=entry, 2=verify, 3=approve, 4=recommend).
            -- We take the MIN() to normalise across multiple role rows per rank.
            step_map AS (
                SELECT
                    scheme_id,
                    rank,
                    MIN(next_label_role_id) AS step_role_id
                FROM workflowstep_rolemappings
                WHERE module_id IS NULL
                GROUP BY scheme_id, rank
            ),

            -- Pivot step_role_ids into columns for easy JOIN
            step_pivot AS (
                SELECT
                    scheme_id,
                    MIN(step_role_id) FILTER (WHERE rank = 1) AS entry_role,
                    MIN(step_role_id) FILTER (WHERE rank = 2) AS verify_role,
                    MIN(step_role_id) FILTER (WHERE rank = 3) AS approve_role,
                    MIN(step_role_id) FILTER (WHERE rank = 4) AS recommend_role
                FROM step_map
                GROUP BY scheme_id
            ),

            -- Active scheme info from current 'schemes' table
            scheme_info AS (
                SELECT id AS scheme_id, name AS scheme_name
                FROM public.schemes
                WHERE is_active = 1
            ),

            -- Tag each beneficiary with their current workflow status
            base AS (
                SELECT
                    b.scheme_id,
                    si.scheme_name,
                    CASE
                        WHEN b.next_level_role_id < 0                      THEN 'rejected'
                        WHEN b.next_level_role_id = sp.entry_role           THEN 'entry'
                        WHEN b.next_level_role_id = sp.verify_role          THEN 'verified'
                        WHEN b.next_level_role_id = sp.approve_role         THEN 'approved'
                        WHEN b.next_level_role_id = sp.recommend_role
                             AND sp.recommend_role IS NOT NULL               THEN 'recomended'
                        ELSE 'unknown'
                    END AS status
                FROM pension.beneficiary_personals AS b
                LEFT JOIN scheme_info              AS si ON si.scheme_id  = b.scheme_id
                LEFT JOIN step_pivot               AS sp ON sp.scheme_id  = b.scheme_id
                WHERE b.next_level_role_id >= 0  -- exclude hard-deleted / invalid rows
            )

            SELECT
                base.scheme_id,
                base.scheme_name,
                COUNT(*) FILTER (WHERE base.status = 'entry')      AS entry_count,
                COUNT(*) FILTER (WHERE base.status = 'verified')   AS verified_count,
                COUNT(*) FILTER (WHERE base.status = 'approved')   AS approved_count,
                COUNT(*) FILTER (WHERE base.status = 'recomended') AS recomended_count,
                COUNT(*) FILTER (WHERE base.status = 'rejected')   AS rejected_count
            FROM base
            GROUP BY base.scheme_id, base.scheme_name

            WITH DATA
        ");

        // ── 4. Unique index: required for CONCURRENT refresh (non-blocking) ─
        DB::connection($this->connection)->statement("
            CREATE UNIQUE INDEX IF NOT EXISTS idx_mv_scheme_status_summary_scheme_id_v1
                ON pension.mv_scheme_status_summary_v1 (scheme_id)
        ");
    }

    public function down(): void
    {
        DB::connection($this->connection)->statement(
            'DROP MATERIALIZED VIEW IF EXISTS pension.mv_scheme_status_summary_v1'
        );
    }
};
