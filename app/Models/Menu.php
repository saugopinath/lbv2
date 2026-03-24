<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menus';

    protected $fillable = [
        'menu_name',
        'icon',
        'url',
        'parent_id',
        'menu_rank',
        'is_active'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Parent Menu
    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    // Child Menus
    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id');
    }

    // Permissions Mapping
    public function permissions()
    {
        return $this->belongsToMany(
            Permission::class,
            'menu_permission_mappings',
            'menu_id',
            'permission_id'
        );
    }
}