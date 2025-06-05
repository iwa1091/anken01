@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection


@section('content')
<div class="container">
    <div class="left-content">
        <!-- 商品情報 -->
        <div class="item-image">
            <img
                src="{{ Str::startsWith($item->img_url, 'http') ? $item->img_url : asset('storage/' . $item->img_url) }}"
                alt="{{ $item->name }}"
                class="card-img-top"
            >
        </div>
    </div>
    <div class="right-content">
        <!-- 商品名 -->
        <h2>{{ $item->name }}</h2>
        <p class="item-text">{{ $item->brand }}</p>
        <p class="item-text">¥{{ number_format($item->price) }}（税込）</p>
        <div class="group">
        <!-- いいねボタン -->
            <div class="like-count__group">
                <form method="POST" action="{{ route('item.like', ['item_id' => $item->id]) }}">
                @csrf
                    <button type="submit" class="like-button {{ $item->is_liked ? 'liked' : '' }}">
                        <img src="{{ asset('/images/star.png') }}" alt="いいね" class="img-star">
                        <span class="like-count">{{ $item->likes_count ?? 0 }}</span>
                    </button>
                </form>
            </div>
            <!-- コメント数 -->
            <div class="comments-count__group">
                <img src="{{ asset('/images/speechballoon.png') }}"  alt="ふきだしの画像" class="img-speechballoon"/>
                <p class="comments-count">{{ $item->comments_count ?? 0 }}</p>
            </div>
        </div>
        <!-- 購入ボタン -->
        <a href="{{ route('purchase.show', ['item_id' => $item->id]) }}" class="btn btn-success">
            購入手続きへ
        </a>
        <!-- 商品説明 -->
        <h2>商品説明</h2>
        <p>{{ $item->description }}</p>

        <h2>商品情報</h2>
        <p><strong>カテゴリー</strong>
            @foreach ($item->categories as $category)
                <span class="badge bg-primary">{{ $category->name }}</span>
            @endforeach
        </p>
        <p><strong>商品の状態:</strong> {{ $item->condition }}</p>
    

    <!-- コメント一覧 -->
        <div class="comments">
            @foreach ($item->comments as $comment)
                <div class="comments-list">
                    <h3>コメント ({{ $comment->id }})</h3>
                    <div class="profile-input-wrapper">
                        @if(isset($comment->user) && $comment->user->profile_image)
                            <img src="{{ asset('storage/' . $comment->user->profile_image) }}" alt="プロフィール画像" class="profile-image-preview">
                        @else
                            <div class="profile-placeholder"></div>
                        @endif
                        <p class="profile-input__user-name"><strong>{{ $comment->user->name ?? '未設定'}}</strong></p>
                    </div>
                    <div class="comments-list__item">
                        <p>{{ $comment->content }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- コメント投稿フォーム（ログインユーザーのみ） -->
        @auth
        <div class="user-comments">
            <h3>商品へのコメント</h3>
            <form method="POST" action="{{ route('item.comment', ['item_id' => $item->id]) }}">
                @csrf
                <textarea name="content" class="form-control" rows="3"></textarea>
                <button type="submit" class="btn comments-button">コメントを送信する</button>
            </form>
        </div>
        @endauth
    </div>
</div>
@endsection