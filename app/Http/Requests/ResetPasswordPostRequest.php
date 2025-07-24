<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\UserIdRule;
use App\Rules\SourceTypeRule;
class ResetPasswordPostRequest extends FormRequest
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
            'user_password' => [
                    'required', 
                    'string', 
                    'min:8', 
                    'regex:/[a-z]/', 
                    'regex:/\d/', 
                    'regex:/[!@#$%^&*(),.?":{}|<>]/'
                ],
            'confirm_user_password' => ['required', 'same:user_password'],
            'captcha' => 'required|captcha'
        ];
    }

    public function messages()
    {
       
        return [
            'user_password.required' => __('messages.Passwordrequired'),
            'user_password.min' => __('messages.Passwordminchar'),
            'user_password.regex' => __('messages.Passwordhealth'),
            'confirm_user_password.required' => __('messages.ConfirmPasswordrequired'),
            'confirm_user_password.same' => __('messages.passwordsame'),
            'captcha.required' => __('messages.Captcharequired'),
            'captcha.captcha' => __('messages.invalidCaptcha'),
        ];
    }
}
