<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Crypt;
class UserIdRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try{
        $user_id = Crypt::decrypt($value);
        $user_id = (int) $user_id;
        if(!is_int($user_id)){
            dd($user_id);
            $fail(__('messages.invalidToken'));     
        }
        }
        catch (\Exception $e) {   
            $fail(__('messages.invalidToken'));     
        }
    }
}
