<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateBenContactDetails extends Command
{
    protected $signature = 'migrate:ben-contact-details';
    protected $description = 'Migrate old main, draft & faulty ben contact details into main table';

    public function handle()
    {
        $this->info('Ben Contact Details migration started...');

        DB::beginTransaction();

        try {

            /**
             * =====================================
             * OLD MAIN (renamed) → MAIN
             * source: lb_scheme.ben_contact
             * =====================================
             */
            DB::statement("
                INSERT INTO lb_scheme.ben_contact_details (
                    application_id,
                    beneficiary_id,
                    dist_code,
                    police_station,
                    rural_urban_id,
                    block_ulb_code,
                    block_ulb_name,
                    block_ulb_type,
                    gp_ward_code,
                    gp_ward_name,
                    village_town_city,
                    house_premise_no,
                    post_office,
                    pincode,
                    residency_period,
                    created_by_level,
                    created_at,
                    updated_at,
                    deleted_at,
                    created_by,
                    ip_address,
                    created_by_dist_code,
                    created_by_local_body_code,
                    jnmp_marked,
                    ds_phase,
                    sr_dist_code,
                    sr_block_ulb_code,
                    sr_block_ulb_name,
                    sr_gp_ward_code,
                    sr_gp_ward_name,
                    action_by,
                    action_ip_address,
                    action_type,
                    source_type
                )
                SELECT
                    m.application_id,
                    m.beneficiary_id,
                    m.dist_code,
                    m.police_station,
                    m.rural_urban_id,
                    m.block_ulb_code,
                    m.block_ulb_name,
                    m.block_ulb_type,
                    m.gp_ward_code,
                    m.gp_ward_name,
                    m.village_town_city,
                    m.house_premise_no,
                    m.post_office,
                    m.pincode,
                    m.residency_period,
                    m.created_by_level,
                    m.created_at,
                    m.updated_at,
                    m.deleted_at,
                    m.created_by,
                    m.ip_address,
                    m.created_by_dist_code,
                    m.created_by_local_body_code,
                    m.jnmp_marked,
                    m.ds_phase,
                    m.sr_dist_code,
                    m.sr_block_ulb_code,
                    m.sr_block_ulb_name,
                    m.sr_gp_ward_code,
                    m.sr_gp_ward_name,
                    m.action_by,
                    m.action_ip_address,
                    m.action_type,
                    0 AS source_type
                FROM lb_scheme.ben_contact m
                WHERE NOT EXISTS (
                    SELECT 1
                    FROM lb_scheme.ben_contact_details b
                    WHERE b.application_id = m.application_id
                )
            ");

            /**
             * =====================================
             * DRAFT → MAIN
             * =====================================
             */
            DB::statement("
                INSERT INTO lb_scheme.ben_contact_details (
                    application_id,
                    dist_code,
                    police_station,
                    rural_urban_id,
                    block_ulb_code,
                    block_ulb_name,
                    block_ulb_type,
                    gp_ward_code,
                    gp_ward_name,
                    village_town_city,
                    house_premise_no,
                    post_office,
                    pincode,
                    residency_period,
                    created_by_level,
                    created_at,
                    updated_at,
                    deleted_at,
                    created_by,
                    ip_address,
                    created_by_dist_code,
                    created_by_local_body_code,
                    ds_phase,
                    action_by,
                    action_ip_address,
                    action_type,
                    source_type
                )
                SELECT
                    d.application_id,
                    d.dist_code,
                    d.police_station,
                    d.rural_urban_id,
                    d.block_ulb_code,
                    d.block_ulb_name,
                    d.block_ulb_type,
                    d.gp_ward_code,
                    d.gp_ward_name,
                    d.village_town_city,
                    d.house_premise_no,
                    d.post_office,
                    d.pincode,
                    d.residency_period,
                    d.created_by_level,
                    d.created_at,
                    d.updated_at,
                    d.deleted_at,
                    d.created_by,
                    d.ip_address,
                    d.created_by_dist_code,
                    d.created_by_local_body_code,
                    d.ds_phase,
                    d.action_by,
                    d.action_ip_address,
                    d.action_type,
                    1 AS source_type
                FROM lb_scheme.draft_ben_contact_details d
                WHERE NOT EXISTS (
                    SELECT 1
                    FROM lb_scheme.ben_contact_details b
                    WHERE b.application_id = d.application_id
                )
            ");

            /**
             * =====================================
             * FAULTY → MAIN
             * =====================================
             */
            DB::statement("
                INSERT INTO lb_scheme.ben_contact_details (
                    application_id,
                    dist_code,
                    police_station,
                    rural_urban_id,
                    block_ulb_code,
                    block_ulb_name,
                    block_ulb_type,
                    gp_ward_code,
                    gp_ward_name,
                    village_town_city,
                    house_premise_no,
                    post_office,
                    pincode,
                    residency_period,
                    created_by_level,
                    created_at,
                    updated_at,
                    deleted_at,
                    created_by,
                    ip_address,
                    created_by_dist_code,
                    created_by_local_body_code,
                    ds_phase,
                    action_by,
                    action_ip_address,
                    action_type,
                    source_type
                )
                SELECT
                    f.application_id,
                    f.dist_code,
                    f.police_station,
                    f.rural_urban_id,
                    f.block_ulb_code,
                    f.block_ulb_name,
                    f.block_ulb_type,
                    f.gp_ward_code,
                    f.gp_ward_name,
                    f.village_town_city,
                    f.house_premise_no,
                    f.post_office,
                    f.pincode,
                    f.residency_period,
                    f.created_by_level,
                    f.created_at,
                    f.updated_at,
                    f.deleted_at,
                    f.created_by,
                    f.ip_address,
                    f.created_by_dist_code,
                    f.created_by_local_body_code,
                    f.ds_phase,
                    f.action_by,
                    f.action_ip_address,
                    f.action_type,
                    2 AS source_type
                FROM lb_scheme.faulty_ben_contact_details f
                WHERE NOT EXISTS (
                    SELECT 1
                    FROM lb_scheme.ben_contact_details b
                    WHERE b.application_id = f.application_id
                )
            ");

            DB::commit();
            $this->info('Ben Contact Details migration completed ✅');

        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Migration failed ❌');
            throw $e;
        }
    }
}
