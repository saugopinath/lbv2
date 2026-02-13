<?php

namespace App\Models;


class BeneficiaryAadhaar extends BaseAuditableModel
{
  protected $guarded = [];
  protected $primaryKey = 'application_id';
  protected $table = 'pension.beneficiary_aadhars';
  public $incrementing = false;
}
