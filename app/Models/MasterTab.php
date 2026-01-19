<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterTab extends Model
{
     protected $table = 'master_tabs'; 
      protected $fillable = [
        'tab_name',
        'tab_code',
        'tab_short_name',
        'tab_component',
        'tab_icon',
        'is_active',
    ];
}
