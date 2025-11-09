<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class AcceptRejectInfo extends Model implements Auditable
{
<<<<<<< HEAD
    use \OwenIt\Auditing\Auditable; 
    
=======
    use \OwenIt\Auditing\Auditable;
>>>>>>> d726694e2ff4cbf8a12d9642a72f953c3c34c7b5
    protected $table = 'accept_reject_infos';

    protected $fillable = [
        'application_id',
        'beneficiary_id',
        'ip_address',
        'user_id',
        'browser',
        'model_name',
        'op_type',
        'revert_reason_cause_id',
        'revert_reason_remarks',
        'parent_id'
    ];

    public function revertReason()
    {
        return $this->belongsTo(Codemaster::class, 'revert_reason_cause_id');
    }

    public function application()
    {
        return $this->belongsTo(BeneficiaryCommonList::class, 'application_id', 'sourceable_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function opType()
    {
        return $this->belongsTo(Codemaster::class, 'op_type');
    }
}
