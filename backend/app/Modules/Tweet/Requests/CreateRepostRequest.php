<?php

namespace App\Modules\Tweet\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateRepostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'linkedTweetId' => 'required|exists:tweets,id',
        ];
    }

    public function messages()
    {
        return [
            'linkedTweetId.exists' => 'Привязанный твит не существует',
        ];
    }
}
