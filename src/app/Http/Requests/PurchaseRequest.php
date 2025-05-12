<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
{
    public function authorize()
    {
        return true; // 認証チェックが必要なら適宜変更
    }

    public function rules()
    {
        return [
            'payment' => 'required|in:credit,convenience',
        ];
    }

    public function messages()
    {
        return [
            'payment.required' => '支払い方法を選択してください。',
            'payment.in' => '選択した支払い方法が無効です。',
        ];
    }
}