<?php

namespace App\Modules\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegistrationCodeRequest extends FormRequest
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
            'code' => 'required|string|size:5'
        ];
    }

    public function messages()
    {
        return [
            'code.required' => 'Код подтверждения обязателен к заполнению.',
            'code.string' => 'Код подтверждения должен быть строкой.',
            'code.size' => 'Код подтверждения должен состоять из 5 символов.'
        ];
    }
}
