<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUserRequest;


class AuthenticatedSessionController extends Controller
{
    /**
     * ログイン処理
     */
    public function store(LoginUserRequest $request)
    {
        $request->authenticate();

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

        return redirect('/login')->with('success', 'ログアウトしました！');
    }

    public function create()
    {
        return view('auth.login'); // resources/views/auth/login.blade.php を表示
    }

}