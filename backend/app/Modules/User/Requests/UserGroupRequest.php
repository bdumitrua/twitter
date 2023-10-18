<?php

namespace App\Modules\User\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            NAME => 'required|string|max:255',
            DESCRIPTION => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            NAME . '.required' => 'Название группы является обязательным полем.',
            NAME . '.string'    => 'Название группы должно быть строкой.',
            NAME . '.max'    => 'Название группы не может быть длиннее 255 символов.',

            'description.string'    => 'Описание группы должно быть строкой.',
            'description.max'    => 'Описание группы не может быть длиннее 255 символов.',
        ];
    }
}
