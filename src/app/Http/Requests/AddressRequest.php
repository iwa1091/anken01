<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    public function authorize()
    {
        return true; // 認証チェックが必要なら適宜変更
    }

    public function rules()
    {
        return [
            'postal_code' => 'nullable|string|max:8',
            'address' => 'required|string|max:255',
            'building_name' => 'nullable|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'postal_code.max' => '郵便番号は8文字以内で入力してください。',
            'address.required' => '住所を入力してください。',
            'address.max' => '住所は255文字以内で入力してください。',
            'building_name.max' => '建物名は255文字以内で入力してください。',
        ];
    }
}