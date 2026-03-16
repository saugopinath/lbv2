<?php

namespace App\Models;

use Laravel\Scout\Searchable;

class BeneficiaryAadhaar extends BaseAuditableModel
{
  use Searchable;
  protected $guarded = [];
  protected $primaryKey = 'application_id';
  protected $table = 'pension.beneficiary_aadhars';
  public $incrementing = false;
  public function searchableAs()
  {
    return 'pension_beneficiary_aadhars';
  }
  public function toSearchableArray()
  {
    return [
      'application_id' => $this->application_id,
      'beneficiary_id' => $this->beneficiary_id,
      'encode_key' => $this->encode_key,
      'encoded_aadhar' => $this->encoded_aadhar,
      'aadhar_vault' => $this->aadhar_vault,
      'aadhar_hash' => $this->aadhar_hash,
      'is_clean' => $this->is_clean,
      'created_at' => $this->created_at,
      'updated_at' => $this->updated_at,
      'scheme_id' => $this->scheme_id,
    ];
  }
}
