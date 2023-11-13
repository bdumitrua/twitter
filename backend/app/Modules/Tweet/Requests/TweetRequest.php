<?php

namespace App\Modules\Tweet\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TweetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'text' => 'nullable|string|max:255',
            'userGroupId' => 'nullable|exists:user_groups,id',
            'type' => 'in:default,repost,reply,quote',
            'linkedTweetId' => 'nullable|exists:tweets,id',
        ];
    }

    public function messages()
    {
        return [
            'text.string' => 'Текст должен быть строкой',
            'text.max' => 'Максимальная длина текста 255 символов',

            'userGroupId.exists' => 'Выбранная группа пользователей не существует',

            'type.in' => 'Недействительный тип твитта',

            'linkedTweetId.exists' => 'Привязанный твит не существует',
        ];
    }
}
