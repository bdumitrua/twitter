<?php

namespace App\Modules\User\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UsersListRequest extends FormRequest
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
            NAME => 'required|string|max:255',
            DESCRIPTION => 'nullable|string|max:255',
            'is_private' => 'nullable|boolean',
            // TODO FILES
            'bg_image' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            NAME . '.required' => 'Название списка является обязательным полем.',
            NAME . '.string'    => 'Название списка должно быть строкой.',
            NAME . '.max'    => 'Название списка не может быть длиннее 255 символов.',

            DESCRIPTION . '.string'    => 'Описание списка должно быть строкой.',
            DESCRIPTION . '.max'    => 'Описание списка не может быть длиннее 255 символов.',

            'is_private.boolean'    => 'Приватность должна быть типа boolean (true/false).',
        ];
    }
}
