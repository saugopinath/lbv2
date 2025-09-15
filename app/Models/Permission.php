<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    use HasFactory;

    protected $guarded = ['id'];

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    protected static function booted()
    {
        static::deleting(function ($permission) {
            $permission->children()->each(function ($child) {
                $child->delete();
            });
        });
    }
    public function validationScore()
    {
        return $this->hasOne(ValidationScoreMapping::class);
    }
}
