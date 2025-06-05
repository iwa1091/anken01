<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ItemRequest extends FormRequest
{
    /**
     * ユーザーがこのリクエストを行うことを認可するかどうかを決定する。
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * リクエストに適用されるバリデーションルールを取得。
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules()
    {
        return [
            'product_image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'product_name' => 'required|string|max:255',
            'product_price' => 'required|numeric|min:0',
            'product_description' => 'nullable|string|max:1000',
            'product_brand' => 'nullable|string|max:255',
        ];
    }

    /**
     * 定義されたバリデーションルールのエラーメッセージを取得します。
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'product_image.image' => '商品画像は画像ファイルである必要があります。',
            'product_image.mimes' => '商品画像はJPEG、PNG、JPG、GIF形式である必要があります。',
            'product_image.max' => '商品画像のサイズは2MBを超えてはなりません。',
            'product_name.required' => '商品名を入力してください。',
            'product_name.string' => '商品名は文字列で入力してください。',
            'product_name.max' => '商品名は255文字以内で入力してください。',
            'product_price.required' => '価格を入力してください。',
            'product_price.numeric' => '価格は数値で入力してください。',
            'product_price.min' => '価格は0円以上でなければなりません。',
            'product_description.string' => '商品説明は文字列で入力してください。',
            'product_description.max' => '商品説明は1000文字以内で入力してください。',
            'product_brand.string' => 'ブランド名は文字列で入力してください。',
            'product_brand.max' => 'ブランド名は255文字以内で入力してください。',
        ];
    }
}
