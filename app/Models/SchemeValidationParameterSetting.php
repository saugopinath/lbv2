<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchemeValidationParameterSetting extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'scheme_validation_parameter_settings';

    protected $fillable = [
        'scheme_id',
        'parameter_code',
        'master_code',
        'is_active',
        'from_affected_date',
        'to_affected_date',
        'min_score',
        'max_score',
    ];
  public function menu()
{
    return $this->belongsTo(Codemaster::class, 'master_code', 'id');
}

public function scheme()
{
    return $this->belongsTo(Scheme::class, 'scheme_id', 'id');
}
public function getParameterNamesAttribute()
    {
        $codes = [];

        if (isset($this->parameter_codes)) {
            $codes = explode(',', $this->parameter_codes);
        } elseif (isset($this->parameter_code)) {
            $codes = explode(',', $this->parameter_code);
        }
        return Codemaster::whereIn('id', $codes)->pluck('name')->toArray();
    }
//  public function parameter()
//     {
//         return $this->belongsTo(Codemaster::class, 'parameter_code', 'id');
//     }
}
