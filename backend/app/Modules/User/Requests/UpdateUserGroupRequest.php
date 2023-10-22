<?php

namespace App\Modules\User\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.string'    => 'Название группы должно быть строкой.',
            'name.max'    => 'Название группы не может быть длиннее 255 символов.',

            'description.string'    => 'Описание группы должно быть строкой.',
            'description.max'    => 'Описание группы не может быть длиннее 255 символов.',
        ];
    }
}
