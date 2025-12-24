<?php
// MasterTab.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterTab extends Model
{
    protected $table = 'master_tabs';
    protected $primaryKey = 'tab_code';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['tab_code', 'tab_name', 'is_active', 'tab_key', 'tab_component'];

    public function schemeMappings()
{
    return $this->hasMany(SchemeTabMapping::class, 'tab_code', 'tab_code');
}
}
