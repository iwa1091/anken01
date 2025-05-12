@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
<div class="container">
    <h1>ログイン</h1>
    <div class="container-form">
        <!-- ログインフォーム -->
        <form method="POST" action="{{ route('login') }}" novalidate>
            @csrf

            <!-- メールアドレス -->
            <div class="form-group">
                <label for="email">メールアドレス</label>
                <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
                @error('email')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <!-- パスワード -->
            <div class="form-group">
                <label for="password">パスワード</label>
                <input type="password" name="password" id="password" class="form-control" required>
                @error('password')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <!-- ログインボタン -->
            <button type="submit" class="btn btn-primary">ログイン</button>
        </form>
    </div>

    <!-- 会員登録画面への動線 -->
    <a href="{{ route('register') }}">会員登録はこちら</a>
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function(){
    document.querySelector('form').setAttribute('novalidate', true);
});
</script>
@endsection