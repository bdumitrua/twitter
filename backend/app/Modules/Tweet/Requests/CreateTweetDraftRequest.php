<?php

namespace App\Modules\Tweet\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTweetDraftRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'text' => 'required|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'text.required' => 'Текст твита является обязательным полем',
            'text.string' => 'Текст должен быть строкой',
            'text.max' => 'Максимальная длина текста 255 символов',
        ];
    }
}
