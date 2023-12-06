<?php

namespace App\Modules\Search\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'search.required' => 'Поиск не может быть по пустому значению.',
            'search.max'    => 'Текст поиска не может быть не длиннее 255 символов.',
        ];
    }
}
