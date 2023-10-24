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
            'text' => 'required|string|max:255',
            'userGroupId' => 'nullable|exists:user_groups,id',
            'isComment' => 'nullable|boolean',
            'commentedTweetId' => 'nullable|exists:tweets,id',
            'isReply' => 'nullable|boolean',
            'repliedTweetId' => 'nullable|exists:tweets,id',
            'isRepost' => 'nullable|boolean',
            'repostedTweetId' => 'nullable|exists:tweets,id',
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
            'commentedTweetId.exists' => 'Комментируемый твитт не существует',

            'isReply.boolean' => 'Поле "isReply" должно быть булевым',
            'repliedTweetId.exists' => 'Цитируемый твитт не существует',

            'isRepost.boolean' => 'Поле "isRepost" должно быть булевым',
            'repostedTweetId.exists' => 'Репостнутый твитт не существует',
        ];
    }
}
