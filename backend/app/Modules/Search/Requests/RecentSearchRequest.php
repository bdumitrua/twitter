<?php

namespace App\Modules\Search\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecentSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'text' => 'required|string|max:255',
            'linkedUserId' => 'nullable|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'text.required' => 'Текст поиска не может быть по пустому значению.',
            'text.max' => 'Текст поиска не может быть не длиннее 255 символов.',

            'linkedUserId.exists' => 'Пользователь не найден.',
        ];
    }
}
