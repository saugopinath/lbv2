<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Crypt;
class SourceTypeRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try{
            $source_type = Crypt::decrypt($value);
            $source_type = (int) $source_type;
            if(!is_int($source_type)){
                $fail(__('messages.invalidSignature'));     
            }
            if (!in_array($source_type, array(1,2))){
                $fail(__('messages.invalidSignature'));     
            }
            
        }    
        catch (\Exception $e) {   
                $fail(__('messages.invalidSignature'));     
        }
    }
}
