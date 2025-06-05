<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'postal_code' => ['nullable', 'regex:/^\d{7}$/'],
            'address' => 'required|string|max:255',
            'building_name' => 'nullable|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'postal_code.regex' => '郵便番号は7桁の半角数字で入力してください（例：1234567）。',
            'address.required' => '住所を入力してください。',
            'address.max' => '住所は255文字以内で入力してください。',
            'building_name.max' => '建物名は255文字以内で入力してください。',
        ];
    }
}
