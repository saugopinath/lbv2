<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use OwenIt\Auditing\Contracts\Auditable;

class CasteModificationInfo extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'pension.caste_modification_infos';
    protected $guarded = ['id'];
    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
    ];
    public function beneficiaryPersonal()
    {
        return $this->hasOne(BeneficiaryPersonalDetail::class, 'application_id', 'application_id');
    }
    public function casteRequestType()
    {
        return $this->belongsTo(Codemaster::class, 'caste_request_type', 'id');
    }
    public function nextLevelRequested()
    {
        return $this->belongsTo(Codemaster::class, 'next_level_requested_id', 'id');
    }
    public function transformAudit(array $data): array
    {
        $userId = Auth::id();
        $userRole = UserRoleSchemeOfficeMapping::where('user_id', $userId)
            ->value('role_id');
        $data['tags'] = class_basename($this) . '_' . $data['event'];
        $data['session_id'] = session()->getId();
        // $data['other_details'] = [
        //     'updated_by_role' => $userRole,
        //     'user_agent' => \Illuminate\Support\Facades\Request::userAgent(),
        //     'url' => \Illuminate\Support\Facades\Request::fullUrl(),
        //     'method' => \Illuminate\Support\Facades\Request::method(),
        //     'referrer' => \Illuminate\Support\Facades\Request::header('referer'),
        // ];
        $data['other_details'] = json_encode([
            'updated_by_role' => $userRole,
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'referrer' => request()->header('referer'),
            
        ]);


        return $data;
    }
}
