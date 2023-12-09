<?php

namespace App\Modules\Tweet\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RemoveTweetDraftsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'drafts' => 'required|array',
            'drafts.*' => 'integer',
        ];
    }

    public function messages()
    {
        return [
            'drafts.required' => 'drafts является обязательным полем',
            'drafts.array' => 'drafts должен быть массивом',
            'drafts.*.integer' => 'drafts должен быть массивом чисел',
        ];
    }
}
