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
            'userGroupId' => 'nullable|exists:user_groups,id',
            'tweets' => 'required|array',
            'tweets.*.text' => 'required|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'userGroupId.exists' => 'Выбранная группа пользователей не существует',

            'tweets.array' => 'Поле tweets должно быть массивом',
            'tweets.required' => 'Массив tweets является обязательным',

            'tweets.*.text.required' => 'Текст твита является обязательным полем',
            'tweets.*.text.string' => 'Текст должен быть строкой',
            'tweets.*.text.max' => 'Максимальная длина текста 255 символов',
        ];
    }
}
