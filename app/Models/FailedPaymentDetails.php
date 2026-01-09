<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class FailedPaymentDetails extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $table = 'failed_payment_details';

    protected $fillable = [
        'dist_code',
        'local_body_code',
        'lot_no',
        'ben_id',
        'status_code',
        'remarks',
        'ifsc',
        'accno',
        'pmt_mode',
        'failed_type',
        'edited_status',
        'is_migrated',
        'lot_month',
        'name_status',
        'name_status_code',
        'name_response',
        'fp_ds_phase',
        'fin_year',
        'mobile_no',
        'application_id',
        'is_sms_send',
        'legacy_validation_failed',
        'ben_name',
        'matching_score',
        'is_previous_approved',
        'failed_process_type',
        'visiting_time',
        'visiting_mark_date',
        'process_complete',
        'tagging_time',
        'is_minor_mismatch',
        'lot_type',
        'updated_details',
        'approve_edited_status',
    ];
}
