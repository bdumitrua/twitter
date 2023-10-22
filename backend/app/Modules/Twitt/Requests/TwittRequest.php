<?php

namespace App\Modules\Twitt\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TwittRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'text' => 'required|string|max:255',
            'userGroupId' => 'nullable|exists:user_groups,id',
            'isComment' => 'nullable|boolean',
            'commentedTwittId' => 'nullable|exists:twitts,id',
            'isReply' => 'nullable|boolean',
            'repliedTwittId' => 'nullable|exists:twitts,id',
            'isRepost' => 'nullable|boolean',
            'repostedTwittId' => 'nullable|exists:twitts,id',
        ];
    }

    public function messages()
    {
        return [
            'text.required' => 'Текст твитта обязателен',
            'text.string' => 'Текст должен быть строкой',
            'text.max' => 'Максимальная длина текста 255 символов',

            'userGroupId.exists' => 'Выбранная группа пользователей не существует',

            'isComment.boolean' => 'Поле "isComment" должно быть булевым',
            'commentedTwittId.exists' => 'Комментируемый твитт не существует',

            'isReply.boolean' => 'Поле "isReply" должно быть булевым',
            'repliedTwittId.exists' => 'Цитируемый твитт не существует',

            'isRepost.boolean' => 'Поле "isRepost" должно быть булевым',
            'repostedTwittId.exists' => 'Репостнутый твитт не существует',
        ];
    }
}
