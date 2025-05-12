<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ItemRequest extends FormRequest
{
    public function authorize()
    {
        // 認可の設定 (必要に応じて)
        return true;
    }

    public function rules()
    {
        return [
            'product_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'product_name' => 'required|string|max:255',
            'product_price' => 'required|numeric|min:0',
            'product_description' => 'nullable|string|max:1000',
        ];
    }

    public function messages()
    {
        return [
            'product_image.required' => '商品画像は必須です。',
            'product_image.image' => '商品画像は画像ファイルである必要があります。',
            'product_name.required' => '商品名を入力してください。',
            'product_price.required' => '価格を入力してください。',
            'product_price.numeric' => '価格は数値で入力してください。',
            'product_price.min' => '価格は0円以上でなければなりません。',
        ];
    }
}