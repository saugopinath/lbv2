<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPageVisitLog extends Model
{
    protected $guarded = [];

    public function User()
    {
        return $this->belongsTo(User::class);
    }
    protected $casts = [
        'request_payload' => 'array',
        'response_payload' => 'array',
    ];
}
