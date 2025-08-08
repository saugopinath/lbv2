<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Block extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

     public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function panchayats()
    {
        return $this->hasMany(Panchayat::class);
    }
}
