<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    /**
     * ユーザーがこのリクエストを行う権限があるかチェック
     */
    public function authorize()
    {
        return true; // 必要に応じて権限確認を追加
    }

    /**
     * バリデーションルールの定義
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'postal_code' => 'nullable|string|max:8',
            'address' => 'nullable|string|max:255',
            'building_name' => 'nullable|string|max:255',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    /**
     * カスタムエラーメッセージを定義
     */
    public function messages()
    {
        return [
            'name.required' => 'ユーザー名を入力してください。',
            'postal_code.max' => '郵便番号は8文字以内で入力してください。',
            'profile_image.image' => 'アップロードするファイルは画像形式である必要があります。',
            'profile_image.mimes' => '画像形式はjpeg, png, jpg, gifのみ使用できます。',
            'profile_image.max' => '画像サイズは2MB以下にしてください。',
        ];
    }
}