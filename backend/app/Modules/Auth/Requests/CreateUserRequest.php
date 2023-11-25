<?php

namespace App\Modules\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'birth_date' => [
                'nullable',
                'before_or_equal:today',
                function ($attribute, $value, $fail) {
                    // Преобразование даты из JavaScript формата в формат 'Y-m-d'
                    $date = \DateTime::createFromFormat('D M d Y H:i:s e+', $value);
                    if (!$date) {
                        $fail("Неверный формат даты");
                    }

                    $this->merge(['birth_date' => $date->format('Y-m-d')]);
                },
            ],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Имя является обязательным полем.',
            'name.string'   => 'Имя должно быть строкой.',
            'name.max'      => 'Имя может быть не длиннее 255 символов.',

            'email.required'    => 'Почта является обязательным полем.',
            'email.email'   => 'Введена некорректная почта.',
            'email.max'     => 'Длина почты может быть не более 255 символов.',
            'email.unique'  => 'Данная почта уже занята.',
        ];
    }
}
