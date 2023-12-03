<?php

namespace App\Modules\Tweet\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateThreadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'tweets' => 'required|array',
            'tweets.*.text' => 'nullable|string|max:255',
            'tweets.*.userGroupId' => 'nullable|exists:user_groups,id',
        ];
    }

    public function messages()
    {
        return [
            'tweets.*.text.string' => 'Текст должен быть строкой',
            'tweets.*.text.max' => 'Максимальная длина текста 255 символов',

            'tweets.*.userGroupId.exists' => 'Выбранная группа пользователей не существует',
        ];
    }
}
