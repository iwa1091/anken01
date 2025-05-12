<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;

class RegisteredUserController extends Controller
{
    /**
     * 会員登録ページの表示
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * 新規会員登録の処理
     */
    public function store(RegisterUserRequest $request)
    {
        // ユーザー登録
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // メール認証のメールを送信する
        $user->sendEmailVerificationNotification();

        return redirect()->route('verification.notice')->with('info', '登録が完了しました！メール認証をしてください！');
    }
}