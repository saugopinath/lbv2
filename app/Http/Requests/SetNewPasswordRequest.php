<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SetNewPasswordRequest extends FormRequest
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
            'password' => 'required',
            'confirm_password' => 'required|same:password',
            'captcha' => 'required|captcha'
        ];
    }
    public function messages()
    {
       
        return [
            'password.required' => __('messages.Passwordrequired'),
            'confirm_password.required' => __('messages.ConfirmPasswordrequired'),
            'confirm_password.same' => __('messages.passwordsame'),
            'captcha.required' => __('messages.Captcharequired'),
            'captcha.captcha' => __('messages.invalidCaptcha'),
        ];
    }
}
