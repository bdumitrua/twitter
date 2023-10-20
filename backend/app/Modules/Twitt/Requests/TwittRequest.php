<?php

namespace App\Modules\Twitt\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TwittRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // 
        ];
    }

    public function messages(): array
    {
        return [
            // 
        ];
    }
}
