<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;
use App\Models\User;

class NewPasswordController extends Controller
{
    /**
     * パスワード再設定ページの表示
     */
    public function create(Request $request)
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * 新しいパスワードの適用処理
     */
    public function store(Request $request)
    {
        // バリデーション
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
            'token' => 'required',
        ], [
            'email.required' => 'メールアドレスを入力してください。',
            'email.email' => 'メールアドレスの形式が正しくありません。',
            'password.required' => 'パスワードを入力してください。',
            'password.min' => 'パスワードは8文字以上で入力してください。',
            'password.confirmed' => 'パスワードが一致しません。',
            'token.required' => 'パスワードリセットのトークンが必要です。',
        ]);

        // パスワードリセット処理
        $status = Password::reset(
            $validated,
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        // 成功時
        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('success', 'パスワードがリセットされました。');
        }

        // 失敗時
        throw ValidationException::withMessages([
            'email' => [__($status)],
        ]);
    }
}