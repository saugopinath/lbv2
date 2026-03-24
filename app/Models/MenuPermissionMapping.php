<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuPermissionMapping extends Model
{
    protected $table = 'menu_permission_mappings';

    protected $fillable = [
        'menu_id',
        'permission_id'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }

    public function permission()
    {
        return $this->belongsTo(Permission::class, 'permission_id');
    }
}