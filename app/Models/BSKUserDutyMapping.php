<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class BSKUserDutyMapping extends Model implements Auditable
{
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'bsk.users_duty_mapping_bsk';

    protected $fillable = [
        'name',
        'email',
        'mobile_no',
        'is_active',
        'bsk_name',
        'bsk_code',
        'ohr_code',
        'deo_code',
        'district_id',
        'district_name',
        'is_rural',
        'sub_division_id',
        'sub_district_name',
        'block_id',
        'block_name',
        'agent_id',
        'id_from_bsk',
        'ticket_no',
        'username',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'agent_id', 'id');
    }
}
