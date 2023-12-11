<?php

namespace App\Modules\User\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
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
            'name' => 'nullable|string|max:255',
            'link' => 'nullable|string|unique:users,link|max:20',
            'email' => 'nullable|string|email|unique:users,email|max:255',
            'password' => 'nullable|string|min:8|max:32',
            'about' => 'nullable|string|max:255',
            'bgImage' => 'nullable|string|max:255',
            'avatar' => 'nullable|string|max:255',
            'statusText' => 'nullable|string|max:500',
            'siteUrl' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'birthDate' => 'nullable|date|date_format:Y-m-d|before_or_equal:today',
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => 'Имя должно быть строкой',
            'name.max' => 'Имя не должно превышать 255 символов',

            'link.string' => 'Ссылка должна быть строкой',
            'link.unique' => 'Эта ссылка уже существует',
            'link.max' => 'Ссылка не должна превышать 20 символов',

            'email.string' => 'Email должен быть строкой',
            'email.email' => 'Введите правильный адрес электронной почты',
            'email.unique' => 'Этот email уже зарегистрирован',
            'email.max' => 'Email не должен превышать 255 символов',

            'password.string' => 'Пароль должен быть строкой',
            'password.min' => 'Пароль должен содержать не менее 8 символов',
            'password.max' => 'Пароль должен содержать не более 32 символов',

            'avatar.string' => 'Аватар должен быть строкой',
            'avatar.max' => 'Аватар не должен превышать 255 символов',

            'bgImage.string' => 'Изображение фона должно быть строкой',
            'bgImage.max' => 'Изображение фона не должно превышать 255 символов',

            'statusText.string' => 'Текст статуса должен быть строкой',
            'statusText.max' => 'Текст статуса не должен превышать 500 символов',

            'siteUrl.string' => 'URL сайта должен быть строкой',
            'siteUrl.max' => 'URL сайта не должен превышать 255 символов',

            'about.string' => '\'О себе\' должен быть строкой',
            'about.max' => '\'О себе\' не должен превышать 255 символов',

            'address.string' => 'Адрес должен быть строкой',
            'address.max' => 'Адрес не должен превышать 255 символов',
        ];
    }
}
