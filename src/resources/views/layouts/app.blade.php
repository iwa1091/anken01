<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>anken01</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
    @yield('js')
</head>
<body>
    <header class="header">
        <div class="header__inner">
            <!-- ロゴ -->
            <div class="header__logo">
                <img src="{{ asset('images/logo.svg') }}" alt="ロゴ">
            </div>
            <nav class="header__nav">
                <ul class="header__list">
                    @if (request()->route()->getName() !== 'register')
                        <!-- 検索フォーム -->
                        <li class="header__list-item">
                            <form action="{{ route('items.search') }}" method="GET">
                                <input class="header__form--search" type="text" name="query" placeholder="なにをお探しですか？" value="{{ request('query') }}">
                            </form>
                        </li>
                        <li class="header__list-item">
                            <form action="/logout" method="post">
                                @csrf
                                <button class="header__form--logout" type="submit">ログアウト</button>
                            </form>
                        </li>
                        <li class="header__list-item">
                            <a href="/mypage" class="header__form--mypage">マイページ</a>
                        </li>
                        <li class="header__list-item">
                            <a href="/sell" class="header__form--list">出品</a>
                        </li>
                    @endif
                </ul>
            </nav>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    @livewireScripts
</body>
</html>
