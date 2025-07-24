<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use App\Rules\UserIdRule;
use App\Rules\SourceTypeRule;
class OtpVerificationRequest extends FormRequest
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
            'token_id' => ['required',new UserIdRule()],
            'source_type' => ['required',new SourceTypeRule()]
        ];
    }
    public function messages()
    {
       
        return [
            'token_id.required' => __('messages.invalidSignature'),
            'source_type.required' => __('messages.invalidSignature'),
        ];
    }
}
