<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class DebugController extends Controller
{
    /**
     * Generate and display a signed URL for verification debugging.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function debugSignedUrl(Request $request)
    {
        // ユーザー認証状態を確認
        if (!auth()->check()) {
            Log::warning('署名付きURLの生成が拒否されました。認証されていないユーザーです。');
            return response()->json([
                'error' => 'ユーザーが認証されていません',
                'authenticated' => false
            ], 403);
        }

        // 署名付きURLを生成
        $url = URL::temporarySignedRoute('verification.verify', now()->addMinutes(60), [
            'id' => auth()->user()->id,
            'hash' => sha1(auth()->user()->email),
        ]);

        // デバッグ情報をログに記録
        Log::info('署名付きURLを生成しました', [
            'signed_url' => $url,
            'user_id' => auth()->user()->id,
            'email_hash' => sha1(auth()->user()->email),
            'expires' => now()->addMinutes(60)->timestamp,
        ]);

        // URLと関連データを返却
        return response()->json([
            'signed_url' => $url,
            'user_id' => auth()->user()->id,
            'email_hash' => sha1(auth()->user()->email),
            'expires' => now()->addMinutes(60)->timestamp,
            'authenticated' => true
        ]);
    }

    /**
     * Debug received signature and request parameters.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function debugRequestParameters(Request $request)
    {
        // リクエスト内の署名とパラメータを確認
        Log::debug('リクエストパラメータのデバッグ中', [
            'received_signature' => $request->query('_signature', 'なし'),
            'received_id' => $request->route('id', 'なし'),
            'received_hash' => $request->route('hash', 'なし'),
            'request_time' => now(),
        ]);

        // デバッグ情報を返却
        return response()->json([
            'received_signature' => $request->query('_signature', 'なし'),
            'received_id' => $request->route('id', 'なし'),
            'received_hash' => $request->route('hash', 'なし'),
        ]);
    }

    /**
     * Send a test email for debugging.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendTestEmail()
    {
        // テストメール送信処理
        Mail::raw('This is a test email sent through Mailhog.', function ($message) {
            $message->to('ri.309189@gmail.com'); // 送信先メールアドレス
            $message->subject('Test Email'); // 件名
        });

        Log::info('テストメールを送信しました', [
            'to' => 'ri.309189@gmail.com',
        ]);

        return response()->json([
            'message' => 'Test email sent successfully.',
            'to' => 'ri.309189@gmail.com'
        ]);
    }
}