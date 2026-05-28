<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class BenPaymentDetailsLB extends Model
{
    //use Searchable;

    protected $connection = 'pgsql_lbpayread';
    protected $table = 'payment.ben_payment_details';
    protected $primaryKey = 'ben_id';

    public $timestamps = false; // set true if table has timestamps

    protected $fillable = [
        "dist_code",
        "ben_id",
        "scheme_id",
        "application_id",
        "ben_status",
        "acc_validated",
        "is_eligible",
        "dup_bank",
        "ss_card_no",
        "mobile_no",
        "ben_name",
        "last_accno",
        "last_ifsc",
        "caste",
        "local_body_code",
        "rural_urban_id",
        "block_ulb_code",
        "gp_ward_code",
        "payment_process",
        "total_amt",
        "total_count",
        "start_yymm",
        "end_yymm",
        "created_at",
        "updated_at",
        "faulty_status",
        "faulty_to_main_date",
        "is_rejected",
        "rejected_date",
        "is_caste_changed",
        "effective_yymm",
        "ds_phase",
        "legacy_validated",
        "name_validated",
        "name_validated_modified",
        "arrear_caste_month",
        "payment_report",
        "payment_update_status",
        "fy_is_migrated",
        "fy_migration_type",
        "jnmp_marked",
        "openning_due_amt",
        "openning_due_count",
        "arrear_lot_status",
        "arrear_lot_type"
    ];

    public function toSearchableArray()
    {
        return [
            "dist_code" => $this->dist_code,
            "ben_id" => $this->ben_id,
            "scheme_id" => $this->scheme_id,
            "application_id" => $this->application_id,
            "ben_status" => $this->ben_status,
            "acc_validated" => $this->acc_validated,
            "is_eligible" => $this->is_eligible,
            "dup_bank" => $this->dup_bank,
            "ss_card_no" => $this->ss_card_no,
            "mobile_no" => $this->mobile_no,
            "ben_name" => $this->ben_name,
            "last_accno" => $this->last_accno,
            "last_ifsc" => $this->last_ifsc,
            "caste" => $this->caste,
            "local_body_code" => $this->local_body_code,
            "rural_urban_id" => $this->rural_urban_id,
            "block_ulb_code" => $this->block_ulb_code,
            "gp_ward_code" => $this->gp_ward_code,
            "payment_process" => $this->payment_process,
            "total_amt" => $this->total_amt,
            "total_count" => $this->total_count,
            "start_yymm" => $this->start_yymm,
            "end_yymm" => $this->end_yymm,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "faulty_status" => $this->faulty_status,
            "faulty_to_main_date" => $this->faulty_to_main_date,
            "is_rejected" => $this->is_rejected,
            "rejected_date" => $this->rejected_date,
            "is_caste_changed" => $this->is_caste_changed,
            "effective_yymm" => $this->effective_yymm,
            "ds_phase" => $this->ds_phase,
            "legacy_validated" => $this->legacy_validated,
            "name_validated" => $this->name_validated,
            "name_validated_modified" => $this->name_validated_modified,
            "arrear_caste_month" => $this->arrear_caste_month,
            "payment_report" => $this->payment_report,
            "payment_update_status" => $this->payment_update_status,
            "fy_is_migrated" => $this->fy_is_migrated,
            "fy_migration_type" => $this->fy_migration_type,
            "jnmp_marked" => $this->jnmp_marked,
            "openning_due_amt" => $this->openning_due_amt,
            "openning_due_count" => $this->openning_due_count,
            "arrear_lot_status" => $this->arrear_lot_status,
            "arrear_lot_type" => $this->arrear_lot_type,

        ];
    }

    public function searchableAs()
    {
        return 'payment_ben_payment_details_lbs';
    }
}