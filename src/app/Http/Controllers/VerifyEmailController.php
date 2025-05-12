<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log; // ログ機能を使用

class VerifyEmailController extends Controller
{
    /**
     * Handle the email verification.
     *
     * @param  \Illuminate\Foundation\Auth\EmailVerificationRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        // デバッグ: ログに記録
        Log::debug('デバッグ情報', [
            'Signature' => $request->query('_signature', '未指定'),
            'ID' => $request->route('id', '未指定'),
            'Hash' => $request->route('hash', '未指定'),
            'Expires' => $request->query('expires', '未指定'),
            'Current Timestamp' => now()->timestamp,
        ]);

        // デバッグ: リクエストデータを画面に表示
        //dd([
            //'Signature' => $request->query('_signature', '未指定'),
            //'ID' => $request->route('id', '未指定'),
            //'Hash' => $request->route('hash', '未指定'),
            //'Expires' => $request->query('expires', '未指定'),
            //'Current Timestamp' => now()->timestamp,
        //]);

        // メール認証を完了
        $request->fulfill();

        // 認証成功をログに記録
        Log::info('メール認証が成功しました', [
            'user_id' => $request->user()->id,
            'email' => $request->user()->email,
        ]);

        // 認証が成功した場合、プロフィール編集画面にリダイレクト
        return redirect()->route('profile.edit')->with('success', 'メール認証が完了しました！プロフィールを設定してください！');
    }
}