<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class BenPaymentDetailsJB extends Model
{

    use Searchable;

    protected $connection = 'pgsql_jbpayread';
    protected $table = 'payment.ben_payment_details';
    protected $primaryKey = 'ben_id';

    public $timestamps = false; // set true if table has timestamps

    protected $fillable = [
        "dist_code",
        "ben_id",
        "scheme_id",
        "last_accno",
        "last_ifsc",
        "ben_status",
        "acc_validated",
        "ben_name",
        "local_body_code",
        "rural_urban_id",
        "block_ulb_code",
        "gp_ward_code",
        "created_at",
        "updated_at",
        "deleted_at",
        "caste",
        "gender",
        "mobile_no",
        "npci_bank_code",
        "applied_at",
        "approval_at",
        "rejected_at",
        "is_eligible",
        "pay_validated",
        "is_rejected",
        "dup_bank",
        "total_amt",
        "total_count",
        "payment_process",
        "payment_start_at",
        "legacy_validation",
        "legacy_validated",
        "lb_imported",
    ];

    public function toSearchableArray()
    {
        return [
            "dist_code" => $this->dist_code,
            "ben_id" => $this->ben_id,
            "scheme_id" => $this->scheme_id,
            "last_accno" => $this->last_accno,
            "last_ifsc" => $this->last_ifsc,
            "ben_status" => $this->ben_status,
            "acc_validated" => $this->acc_validated,
            "ben_name" => $this->ben_name,
            "local_body_code" => $this->local_body_code,
            "rural_urban_id" => $this->rural_urban_id,
            "block_ulb_code" => $this->block_ulb_code,
            "gp_ward_code" => $this->gp_ward_code,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "deleted_at" => $this->deleted_at,
            "caste" => $this->caste,
            "gender" => $this->gender,
            "mobile_no" => $this->mobile_no,
            "npci_bank_code" => $this->npci_bank_code,
            "applied_at" => $this->applied_at,
            "approval_at" => $this->approval_at,
            "rejected_at" => $this->rejected_at,
            "is_eligible" => $this->is_eligible,
            "pay_validated" => $this->pay_validated,
            "is_rejected" => $this->is_rejected,
            "dup_bank" => $this->dup_bank,
            "total_amt" => $this->total_amt,
            "total_count" => $this->total_count,
            "payment_process" => $this->payment_process,
            "payment_start_at" => $this->payment_start_at,
            "legacy_validation" => $this->legacy_validation,
            "legacy_validated" => $this->legacy_validated,
            "lb_imported" => $this->lb_imported,

        ];
    }

    public function searchableAs()
    {
        return 'payment_ben_payment_details_jbs';
    }
}
