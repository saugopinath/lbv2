<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchemeTabMapping extends Model
{
    protected $table = 'scheme_tab_mappings';

    protected $fillable = [
        'scheme_id',
        'tab_code',
        'position',
        'is_finally_submitted',
        'is_active'
    ];

    /**
     * Get the master tab that this mapping belongs to.
     */
    public function masterTab(): BelongsTo
    {
        return $this->belongsTo(MasterTab::class, 'tab_code', 'tab_code');
    }

    /**
     * Get the scheme that owns this mapping.
     */
    public function scheme(): BelongsTo
    {
        return $this->belongsTo(Scheme::class);
    }
}
