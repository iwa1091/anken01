<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExhibitionRequest extends FormRequest
{
    /**
     * ユーザーがこのリクエストを許可されているかどうかを判定
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // このリクエストを常に許可
    }

    /**
     * このリクエストに関連するバリデーションルール
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255', // 商品名: 入力必須、最大255文字
            'description' => 'required|string|max:255', // 商品説明: 入力必須、最大255文字
            'price' => 'required|numeric|min:0', // 商品価格: 入力必須、数値型、0円以上
            'category' => 'required|array', // カテゴリー: 必須、配列
            'category.*' => 'exists:categories,id', // カテゴリIDがデータベースに存在することを確認
            'condition' => 'required|string|in:excellent,good,fair,poor', // 状態: 必須、限定された値
            //'image' => 'required|image|mimes:jpeg,png|max:2048', // 商品画像: 必須、JPEGまたはPNG、最大2MB
            //'item_id' => 'required|exists:items,id',
        ];
    }

    /**
     * バリデーションエラー時のカスタムメッセージ
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => '商品名は必須です。',
            'name.max' => '商品名は最大255文字まで入力してください。',
            'description.required' => '商品説明は必須です。',
            'description.max' => '商品説明は最大255文字まで入力してください。',
            'price.required' => '価格は必須です。',
            'price.numeric' => '価格は数値で入力してください。',
            'price.min' => '価格は0円以上である必要があります。',
            //'item_id.required' => '商品IDがありません。適切な商品を選択してください。',
            //'item_id.exists' => '選択した商品IDが無効です。',
            'category.required' => 'カテゴリーを選択してください。',
            'category.*.exists' => '選択されたカテゴリーは存在しません。',
            'condition.required' => '商品の状態を選択してください。',
            'condition.in' => '商品の状態が無効です。',
            //'image.required' => '商品画像は必須です。',//
            //'image.image' => '商品画像は画像形式である必要があります。',//
            //'image.mimes' => '商品画像はJPEGまたはPNG形式である必要があります。',//
            //'image.max' => '商品画像のサイズは2MB以下である必要があります。',//
        ];
    }
}