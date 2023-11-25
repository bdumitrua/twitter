<?php

namespace App\Modules\Auth\Requests;

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
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'Почта является обязательным полем.',
            'email.email'    => 'Введена некорректная почта.',
            'email.max'    => 'Длина почты может быть не более 255 символов.',

            'password.required' => 'Пароль является обязательным полем',
            'password.min' => 'Длина пароля должна быть 8 и более символов',
        ];
    }
}
