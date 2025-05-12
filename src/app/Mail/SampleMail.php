<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SampleMail extends Mailable
{
    use Queueable, SerializesModels;

    // データを格納するためのプロパティ
    public $userName; // ユーザー名
    public $verificationUrl; // 認証リンク

    /**
     * コンストラクタでデータを受け取る
     *
     * @param string $userName
     * @param string $verificationUrl
     */
    public function __construct($userName, $verificationUrl)
    {
        $this->userName = $userName;
        $this->verificationUrl = $verificationUrl;
    }

    /**
     * メールの構築
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('auth.verify-email') // ビューを指定
                    ->subject('【フリマアプリ】メール認証のご案内') // 件名を設定
                    ->with([
                        'userName' => $this->userName, // ユーザー名を渡す
                        'verificationUrl' => $this->verificationUrl, // 認証リンクを渡す
                    ]);
    }
}