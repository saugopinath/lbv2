<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
class DraftBeneficiaryBank extends Model implements Auditable
{
     use \OwenIt\Auditing\Auditable;
     protected $guarded = [
          'id',
     ];
     protected $table = 'lb_scheme.draft_beneficiary_banks';
     protected $primaryKey = 'application_id';
     public function ifscbranch()
     {
          return $this->belongsTo(IfscCodeMaster::class, 'ifsc', 'code');
     }
     public function personal()
     {
          return $this->belongsTo(DraftBeneficiaryPersonal::class, 'application_id', 'application_id');
     }
     protected static function booted()
     {
          static::created(function ($bank) {
               $personal = $bank->personal;
               if ($personal && $personal->lists) {
                    $personal->lists()->update([
                         'bank_account_number' => $bank->bank_account_number,
                    ]);
               }
          });
          static::updated(function ($bank) {
               $personal = $bank->personal;
               if ($personal && $personal->lists) {
                    $personal->lists()->update([
                         'bank_account_number' => $bank->bank_account_number,
                    ]);
               }
          });
     }
}
