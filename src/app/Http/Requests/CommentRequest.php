<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends FormRequest
{
    public function authorize()
    {
        return true; // ログインユーザーのみ許可
    }

    public function rules()
    {
        return [
            'content' => 'required|string|max:255',
        ];
    }
}