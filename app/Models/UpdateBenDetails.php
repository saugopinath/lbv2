<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class UpdateBenDetails extends Model
{
    protected $table = 'lb_scheme.update_ben_details';

    protected $fillable = [
        'failed_tbl_id',
        'beneficiary_id',
        'old_data',
        'new_data',
        'user_id',
        'remarks',
        'update_code',
        'next_level_role_id',
        'dist_code',
        'local_body_code',
        'rural_urban_id',
        'block_ulb_code',
        'gp_ward_code',
        'pmt_mode',
        'failed_type',
        'application_id',
        'ticket_id',
        'ip_address',
        'name_resposne_from_bank',
        'ben_name',
        'legacy_validation_update',
        'approved_remarks',
        'reactive_reason',
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
        'legacy_validation_update' => 'boolean',
    ];
}
