<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use OwenIt\Auditing\Contracts\Auditable;

abstract class BaseAuditableModel extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $guarded = ['id'];

    /**
     * Global audit transformation
     */
    public function transformAudit(array $data): array
    {
        $userId = Auth::id();
        $userRole = UserRoleSchemeOfficeMapping::where('user_id', $userId)->first()->role_id;
        $data['tags'] = class_basename($this) . '_' . $data['event'];
        $data['new_values']['updated_by_role'] = $userRole;
        $data['new_values']['session_id'] = session()->getId();
        $data['new_values']['user_agent'] = \Illuminate\Support\Facades\Request::userAgent();
        $data['new_values']['url'] = \Illuminate\Support\Facades\Request::fullUrl();
        $data['new_values']['method'] = \Illuminate\Support\Facades\Request::method();
        $data['new_values']['referrer'] = \Illuminate\Support\Facades\Request::header('referer');
        return $data;
    }
}
