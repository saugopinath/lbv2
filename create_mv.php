<?php

use Illuminate\Support\Facades\DB;

$sql = <<<SQL
DROP MATERIALIZED VIEW IF EXISTS home.mv_scheme_status_summary;
DROP MATERIALIZED VIEW IF EXISTS pension.mv_scheme_status_summary;

CREATE MATERIALIZED VIEW pension.mv_scheme_status_summary
TABLESPACE pg_default
AS
 WITH step_map AS (
         SELECT workflow_steps.scheme_id,
            workflow_steps.rank AS step_id,
            workflow_steps.parent_id
           FROM public.workflow_steps
        ), scheme_info AS (
         SELECT schemes.id AS scheme_id,
            schemes.name AS scheme_name
           FROM public.schemes
          WHERE schemes.is_active = 1
        ), base AS (
         SELECT b.scheme_id,
            si.scheme_name,
                CASE
                    WHEN b.next_level_role_id < 0 THEN 'rejected'::text
                    WHEN b.next_level_role_id IS NOT DISTINCT FROM s1.parent_id THEN 'entry'::text
                    WHEN b.next_level_role_id IS NOT DISTINCT FROM s2.parent_id THEN 'verified'::text
                    WHEN b.next_level_role_id IS NOT DISTINCT FROM s3.parent_id THEN 'approved'::text
                    WHEN b.next_level_role_id IS NOT DISTINCT FROM s4.parent_id THEN 'recomended'::text
                    ELSE 'unknown'::text
                END AS status
           FROM pension.beneficiary_personals b
             LEFT JOIN scheme_info si ON si.scheme_id = b.scheme_id
             LEFT JOIN step_map s1 ON s1.scheme_id = b.scheme_id AND s1.step_id = 1
             LEFT JOIN step_map s2 ON s2.scheme_id = b.scheme_id AND s2.step_id = 2
             LEFT JOIN step_map s3 ON s3.scheme_id = b.scheme_id AND s3.step_id = 3
             LEFT JOIN step_map s4 ON s4.scheme_id = b.scheme_id AND s4.step_id = 4
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
WITH DATA;

CREATE UNIQUE INDEX idx_mv_scheme_status_summary
    ON pension.mv_scheme_status_summary USING btree
    (scheme_id)
    TABLESPACE pg_default;
SQL;

DB::connection('pgsql_app_read')->unprepared($sql);
echo "Materialized view created successfully!\n";
