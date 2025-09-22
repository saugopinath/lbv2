<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Codemaster extends Model
{
    protected $fillable = [
        'name',
        'short_name',
        'parent_id',
        'code',
    ];
    public function parent()
    {
        return $this->belongsTo(Codemaster::class, 'parent_id');
    }
    public function children()
    {
        return $this->hasMany(Codemaster::class, 'parent_id');
    }
    public static function getIdByCode($code)
    {
        return self::where('code', $code)->value('id');
    }
     public function menus()
    {
        return $this->hasMany(SchemeValidationParameterSetting::class, 'master_code', 'id');
    }

    public function parameters()
    {
        return $this->hasMany(SchemeValidationParameterSetting::class, 'parameter_code', 'id');
    }

}
