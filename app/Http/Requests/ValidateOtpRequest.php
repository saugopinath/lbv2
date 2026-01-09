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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'token_id' => ['required',new UserIdRule()],
            'source_type' => ['required',new SourceTypeRule()],
            'otp' => 'required|regex:/[0-9]{6}/|digits:6',
            'captcha' => 'required|captcha',

        ];
    }
    public function messages()
    {
       
        return [
            'token_id.required' => __('messages.invalidSignature'),
            'source_type.required' => __('messages.invalidSignature'),
            'otp.required' => __('messages.Otprequired'),
            'otp.regex' => __('messages.invalidOtp'),
            'otp.digits' => __('messages.otp6digit'),
            'captcha.required' => __('messages.Captcharequired'),
            'captcha.captcha' => __('messages.invalidCaptcha'),
            
        ];
    }
}
