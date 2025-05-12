@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/verify_email.css') }}">
@endsection

@section('content')
<div class="container">
    <!-- 認証メール送付メッセージ -->
    <p>登録していただいたメールアドレスに認証メールを送付しました。</p>
    
    <!-- 認証完了メッセージ -->
    <p>メール認証を完了してください。</p>
    
    <!-- 認証リンクボタン -->
    <p>
        <a href="{{ URL::signedRoute('verification.verify', ['id' => auth()->user()->id, 'hash' => sha1(auth()->user()->email)]) }}">
        認証はこちらから
        </a>
    </p>
    
    <!-- 再送ボタン -->
    <p>
        <form method="POST" action="{{ route('verification.resend') }}">
            @csrf
            <button type="submit" class="btn btn-secondary">認証メールを再送する</button>
        </form>
    </p>
</div>
@endsection