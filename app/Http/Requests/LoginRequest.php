<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
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
            'mobile_no' => 'required|regex:/[0-9]{10}/|digits:10',
            'password' => 'required',
            'captcha' => 'required|captcha'
        ];
    }
    public function messages()
    {
       
        return [
            'mobile_no.required' => __('messages.mobilerequired'),
            'mobile_no.regex' => __('messages.invalidMobile'),
            'mobile_no.digits' => __('messages.mobile10digit'),
            'password.required' => __('messages.Passwordrequired'),
            'captcha.required' => __('messages.Captcharequired'),
            'captcha.captcha' => __('messages.invalidCaptcha'),
        ];
    }
}
