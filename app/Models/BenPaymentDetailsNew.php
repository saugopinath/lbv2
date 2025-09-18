<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class BenPaymentDetailsNew extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
     protected $table = 'ben_payment_details_new';
}
