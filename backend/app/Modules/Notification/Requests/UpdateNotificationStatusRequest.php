<?php

namespace App\Modules\Notification\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNotificationStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'required|string|in:sended,readed,deleted',
        ];
    }

    public function messages(): array
    {
        return [
            'status.required'   => 'Статус уведомления является обязательным.',
            'status.string'     => 'Статус уведомления должен быть в виде строки.',
            'status.in'         => 'Некорректный статус уведомления.',
        ];
    }
}
