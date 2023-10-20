<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegistrationRequest extends FormRequest
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
        return array_merge([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);
    }

    public function messages()
    {
        return array_merge(parent::messages(), [
            'name.required' => 'Имя является обязательным полем.',
            'name.string'   => 'Имя должно быть строкой.',
            'name.max'      => 'Имя может быть не длиннее 255 символов.',

            'email.required'    => 'Почта является обязательным полем.',
            'email.email'   => 'Введена некорректная почта.',
            'email.max'     => 'Длина почты может быть не более 255 символов.',
            'email.unique'  => 'Данная почта уже занята.',

            'password.required' => 'Пароль является обязательным полем',
            'password.min' => 'Длина пароля должна быть 8 и более символов',
        ]);
    }
}
