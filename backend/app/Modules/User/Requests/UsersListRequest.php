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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'is_private' => 'nullable|boolean',
            // TODO FILES
            'bg_image' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name' . '.required' => 'Название списка является обязательным полем.',
            'name' . '.string'    => 'Название списка должно быть строкой.',
            'name' . '.max'    => 'Название списка не может быть длиннее 255 символов.',

            'description' . '.string'    => 'Описание списка должно быть строкой.',
            'description' . '.max'    => 'Описание списка не может быть длиннее 255 символов.',

            'is_private.boolean'    => 'Приватность должна быть типа boolean (true/false).',
        ];
    }
}
