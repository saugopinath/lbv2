<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicantIncompletDeatil extends Model
{
     protected $table = 'applicant_incomplet_deatils';

    protected $fillable = [
        'application_id',
        'beneficiary_id',
        'incomplet_type',
        'next_level_request_id',
        'new_value',
        'old_value',
        'request_id',
    ];

    public function incompletType()
    {
        return $this->belongsTo(Codemaster::class, 'incomplet_type', 'code');
    }
}
