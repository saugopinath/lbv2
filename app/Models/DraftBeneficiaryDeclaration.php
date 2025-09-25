<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
class DraftBeneficiaryDeclaration extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $guarded = [
        'id',
    ];
     protected $table = 'lb_scheme.draft_beneficiary_declarations';
}
