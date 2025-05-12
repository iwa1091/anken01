<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Http\Controllers\Controller;

class PasswordResetLinkController extends Controller
{
    /**
     * パスワードリセットリンク送信ページの表示
     */
    public function create()
    {
        return view('auth.forgot-password');
    }

    /**
     * パスワードリセットリンクの送信処理
     */
    public function store(Request $request)
    {
        // バリデーション
        $validated = $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'メールアドレスを入力してください。',
            'email.email' => 'メールアドレスの形式が正しくありません。',
        ]);

        // リセットリンクの送信
        $status = Password::sendResetLink($validated);

        // 成功時
        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', __($status));
        }

        // 失敗時
        return back()->withErrors(['email' => __($status)]);
    }
}