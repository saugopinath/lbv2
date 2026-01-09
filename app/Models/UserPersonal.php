<?php




namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class UserPersonal extends Model implements Auditable
{

    use \OwenIt\Auditing\Auditable;
    protected $guarded = [
        'id',
    ];
}
