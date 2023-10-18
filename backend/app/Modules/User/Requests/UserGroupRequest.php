<?php

namespace App\Modules\User\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserGroupRequest extends FormRequest
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
            'user_id' => 'required|integer',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'user_id.required' => 'id пользователя является обязательным полем.',
            'user_id.integer' => 'id пользователя должен быть числом.',

            'name.required' => 'Название группы является обязательным полем.',
            'name.string'    => 'Название группы должно быть строкой.',
            'name.max'    => 'Название группы не может быть длиннее 255 символов.',

            'description.string'    => 'Описание группы должно быть строкой.',
            'description.max'    => 'Описание группы не может быть длиннее 255 символов.',
        ];
    }
}
