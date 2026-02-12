<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use OwenIt\Auditing\Contracts\Auditable;

abstract class BaseAuditableModel extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $guarded = ['id'];
    public function transformAudit(array $data): array
    {
        $userId = Auth::id();
        $userRole = UserRoleSchemeOfficeMapping::where('user_id', $userId)
            ->value('role_id');
        $data['tags'] = class_basename($this) . '_' . $data['event'];
        $data['session_id'] = session()->getId();
        $data['other_details'] = [
            'updated_by_role' => $userRole,
            'user_agent' => \Illuminate\Support\Facades\Request::userAgent(),
            'url' => \Illuminate\Support\Facades\Request::fullUrl(),
            'method' => \Illuminate\Support\Facades\Request::method(),
            'referrer' => \Illuminate\Support\Facades\Request::header('referer'),
        ];
        return $data;
    }
}