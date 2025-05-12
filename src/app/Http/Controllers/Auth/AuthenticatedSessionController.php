<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;

class AuthenticatedSessionController extends Controller
{
    /**
     * ログイン処理
     */
    public function store(Request $request)
    {
        // 入力値のバリデーション
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'メールアドレスを入力してください。',
            'email.email' => 'メールアドレスの形式が正しくありません。',
            'password.required' => 'パスワードを入力してください。',
        ]);

        // 認証試行
        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => __('ログイン情報が正しくありません'),
            ]);
        }

        // セッションを再生成してセキュリティ強化
        $request->session()->regenerate();

        return redirect()->route('items.index')->with('success', 'ログイン成功！');
    }

    /**
     * ログアウト処理
     */
    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'ログアウトしました！');
    }
}