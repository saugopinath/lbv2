<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class BenFailedPaymentDetailsJB extends Model
{
    //use Searchable;
    protected $connection = 'pgsql_jbpayread';
    protected $table = 'payment.failed_payment_details';
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        "id",
        "dist_code",
        "ben_id",
        "scheme_id",
        "lot_no",
        "accno",
        "ifsc",
        "pmt_mode",
        "failed_type",
        "edited_status",
        "lot_month",
        "status_code",
        "remarks",
        "fin_year",
        "created_at",
        "updated_at",
        "deleted_at",
        "lot_type",
        "av_status_code",
        "av_name_response",
        "input_file_name",
        "failed_process_type",
        "updated_details",
        "local_body_code",
        "ben_name",
        "failed_marked",
        "matching_score",
        "if_previous_approve",
        "approve_edited_status",
        "visiting_time",
        "visiting_mark_date",
        "failed_payment_details",
        "process_complete",
        "tagging_time"
    ];

    public function toSearchableArray()
    {
        return [
            "id" => $this->id,
            "dist_code" => $this->dist_code,
            "ben_id" => $this->ben_id,
            "scheme_id" => $this->scheme_id,
            "lot_no" => $this->lot_no,
            "accno" => $this->accno,
            "ifsc" => $this->ifsc,
            "pmt_mode" => $this->pmt_mode,
            "failed_type" => $this->failed_type,
            "edited_status" => $this->edited_status,
            "lot_month" => $this->lot_month,
            "status_code" => $this->status_code,
            "remarks" => $this->remarks,
            "fin_year" => $this->fin_year,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "deleted_at" => $this->deleted_at,
            "lot_type" => $this->lot_type,
            "av_status_code" => $this->av_status_code,
            "av_name_response" => $this->av_name_response,
            "input_file_name" => $this->input_file_name,
            "failed_process_type" => $this->failed_process_type,
            "updated_details" => $this->updated_details,
            "local_body_code" => $this->local_body_code,
            "ben_name" => $this->ben_name,
            "failed_marked" => $this->failed_marked,
            "matching_score" => $this->matching_score,
            "if_previous_approve" => $this->if_previous_approve,
            "approve_edited_status" => $this->approve_edited_status,
            "visiting_time" => $this->visiting_time,
            "visiting_mark_date" => $this->visiting_mark_date,
            "failed_payment_details" => $this->failed_payment_details,
            "process_complete" => $this->process_complete,
            "tagging_time" => $this->tagging_time

        ];
    }

    public function searchableAs()
    {
        return 'payment_ben_failed_payment_details_jbs';
    }
}

