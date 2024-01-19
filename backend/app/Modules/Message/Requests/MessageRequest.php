<?php

namespace App\Modules\Message\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'text' => 'nullable|string',
            'linkedEntityId' => 'nullable|integer|min:1',
            'linkedEntityType' => 'nullable|string|in:tweet',
        ];
    }

    public function messages(): array
    {
        return [
            'text.string' => 'Текст должен быть строкой.',

            'linkedEntityId.integer' => 'Идентификатор связанной сущности должен быть целым числом.',
            'linkedEntityId.min' => 'Идентификатор связанной сущности должен быть не меньше 1.',

            'linkedEntityType.string' => 'Тип связанной сущности должен быть строкой.',
            'linkedEntityType.in' => 'Тип связанной сущности должен быть одним из следующих значений: tweet.',
        ];
    }
}
