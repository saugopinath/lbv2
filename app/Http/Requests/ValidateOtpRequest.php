<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\UserIdRule;
use App\Rules\SourceTypeRule;
class ValidateOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'otp' => 'required|regex:/[0-9]{6}/|digits:6',
            'captcha' => 'required|captcha',

        ];
    }
    public function messages()
    {
       
        return [
            'otp.required' => __('messages.Otprequired'),
            'otp.regex' => __('messages.invalidOtp'),
            'otp.digits' => __('messages.otp6digit'),
            'captcha.required' => __('messages.Captcharequired'),
            'captcha.captcha' => __('messages.invalidCaptcha'),
            
        ];
    }
}
