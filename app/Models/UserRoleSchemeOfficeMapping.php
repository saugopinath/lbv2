<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;
class UserRoleSchemeOfficeMapping extends Model
{
    protected $fillable = [
            'user_id',
            'office_id',
            'scheme_id',
            'role_id'
        ];
     protected static function booted(): void
    {
       
        /**
         * Call After Post Create
         */
        static::created(function (UserRoleSchemeOfficeMapping $mapdata) {
             
           $mapdataArr=$mapdata->toArray();
           $role_list=[];
           $user_id=$mapdataArr['user_id'];
        //    $scheme_id=$mapdataArr['scheme_id'];
           $user=User::find($user_id);
           $role_id=$mapdataArr['role_id'];
           $role = Role::find($role_id);
           $user->assignRole($role);
          
        });


    }
   public function User(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function Office(): BelongsTo
    {
        return $this->belongsTo(OfficeMaster::class);
    }
    public function Scheme(): BelongsTo
    {
        return $this->belongsTo(Scheme::class);
    }
    public function Role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
    
   
}