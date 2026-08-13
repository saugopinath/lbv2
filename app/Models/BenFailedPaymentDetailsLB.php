<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class BenFailedPaymentDetailsLB extends Model
{
    use Searchable;
    protected $connection = 'pgsql_lbpayread';
    protected $table = 'lb_main.failed_payment_details';
    protected $primaryKey = 'id';


    protected $fillable = [
        "id",
        "dist_code",
        "local_body_code",
        "lot_no",
        "ben_id",
        "status_code",
        "remarks",
        "ifsc",
        "accno",
        "pmt_mode",
        "failed_type",
        "edited_status",
        "created_at",
        "updated_at",
        "is_migrated",
        "lot_month",
        "name_status",
        "name_status_code",
        "name_response",
        "fp_ds_phase",
        "fin_year",
        "mobile_no",
        "application_id",
        "is_sms_send",
        "legacy_validation_failed",
        "ben_name",
        "matching_score",
        "is_previous_approved",
        "failed_process_type",
        "visiting_time",
        "visiting_mark_date",
        "process_complete",
        "tagging_time",
        "is_minor_mismatch",
        "lot_type",
        "updated_details",
        "approve_edited_status"
    ];

    public function toSearchableArray()
    {
        return [
            "id" => $this->id,
            "dist_code" => $this->dist_code,
            "local_body_code" => $this->local_body_code,
            "lot_no" => $this->lot_no,
            "ben_id" => $this->ben_id,
            "status_code" => $this->status_code,
            "remarks" => $this->remarks,
            "ifsc" => $this->ifsc,
            "accno" => $this->accno,
            "pmt_mode" => $this->pmt_mode,
            "failed_type" => $this->failed_type,
            "edited_status" => $this->edited_status,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "is_migrated" => $this->is_migrated,
            "lot_month" => $this->lot_month,
            "name_status" => $this->name_status,
            "name_status_code" => $this->name_status_code,
            "name_response" => $this->name_response,
            "fp_ds_phase" => $this->fp_ds_phase,
            "fin_year" => $this->fin_year,
            "mobile_no" => $this->mobile_no,
            "application_id" => $this->application_id,
            "is_sms_send" => $this->is_sms_send,
            "legacy_validation_failed" => $this->legacy_validation_failed,
            "ben_name" => $this->ben_name,
            "matching_score" => $this->matching_score,
            "is_previous_approved" => $this->is_previous_approved,
            "failed_process_type" => $this->failed_process_type,
            "visiting_time" => $this->visiting_time,
            "visiting_mark_date" => $this->visiting_mark_date,
            "process_complete" => $this->process_complete,
            "tagging_time" => $this->tagging_time,
            "is_minor_mismatch" => $this->is_minor_mismatch,
            "lot_type" => $this->lot_type,
            "updated_details" => $this->updated_details,
            "approve_edited_status" => $this->approve_edited_status,
        ];
    }

    public function searchableAs()
    {
        return 'lb_main_ben_failed_payment_details_lbs';
    }
}

