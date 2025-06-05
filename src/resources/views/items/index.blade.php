@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="container">
    <h1>商品一覧</h1>

    <!-- 切り替えボタン -->
    <div class="toggle-buttons">
        <a href="{{ route('items.index', ['tab' => 'recommend', 'query' => request('query')]) }}"
        class="toggle-button {{ ($activeTab ?? 'recommend') === 'recommend' ? 'active' : '' }}">
            おすすめ
        </a>
        <a href="{{ route('items.index', ['tab' => 'mylist', 'query' => request('query')]) }}"
        class="toggle-button {{ ($activeTab ?? 'recommend') === 'mylist' ? 'active' : '' }}">
            マイリスト
        </a>
    </div>


    <!-- メッセージ表示 -->
    @if (!empty($message))
        <div class="alert alert-warning">{{ $message }}</div>
    @endif

    <!-- 商品リスト -->
    <div class="item-list">
        @if (!empty($items) && count($items) > 0)
            @foreach ($items as $item)
                <div class="item">
                    <a href="{{ route('items.detail', ['item_id' => $item->id]) }}" class="item-link">
                        <img
                            src="{{ Str::startsWith($item->img_url, 'http') ? $item->img_url : asset('storage/' . $item->img_url) }}"
                            alt="{{ $item->name }}"
                            class="item-image"
                        >
                        <h2 class="item-name">{{ $item->name }}</h2>
                    </a>
                    <div class="item-details">
                        <p class="item-price">¥{{ number_format($item->price) }}</p>
                        @if ($item->is_sold)
                            <p class="item-sold">Sold</p>
                        @endif
                    </div>
                </div>
            @endforeach
        @else
            <p>商品がありません。</p>
        @endif
    </div>
</div>
@endsection
