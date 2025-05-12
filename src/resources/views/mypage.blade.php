@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endsection

@section('content')
<div class="container">
    <h1 class="mypage-title">マイページ</h1>
    <!-- プロフィール情報 -->
    <div class="profile-section">
        <div class="profile-image">
            @if(isset($user) && $user->profile_image)
                <img src="{{ asset('storage/' . $user->profile_image) }}" alt="プロフィール画像" class="profile-image-preview">
            @else
                <div class="profile-placeholder"></div>
            @endif
        </div>
        <div class="profile-details">
            <h2>{{ Auth::user()->name }}</h2>
            <a href="{{ route('profile.edit') }}" class="btn btn-primary">プロフィールを編集</a>
        </div>
    </div>

    <div class="tab-header">
        <span class="tab active" data-target="listed">出品した商品</span>
        <span class="tab" data-target="purchased">購入した商品</span>
    </div>

    <div class="line"></div>

    <!-- 出品した商品一覧 -->
    <div class="tab-content" id="listed">
        <div class="item-list">
           @foreach($listedItems as $exhibition)
                @if(isset($exhibition->item)) <!-- 商品データがあるか確認 -->
                    <div class="item">
                        <img src="{{ asset('storage/' . $exhibition->item->img_url) }}" alt="{{ $exhibition->item->name }}" class="item-image">
                        <h3>{{ $exhibition->item->name }}</h3>
                        <p>¥{{ number_format($exhibition->item->price) }}</p>
                        <span class="status">{{ $exhibition->item->is_sold ? 'Sold' : '' }}</span>
                    </div>
                @else
                    <p>購入した商品データが見つかりません。</p>
                @endif
            @endforeach
        </div>
    </div>

    <!-- 購入した商品一覧 -->
    <div class="tab-content" id="purchased">
        <div class="item-list">
           @foreach($purchasedItems as $purchase)
                @if(isset($purchase->item)) <!-- 商品データがあるか確認 -->
                    <div class="item">
                        <img src="{{ $purchase->item->img_url }}" alt="{{ $purchase->item->name }}" class="item-image">
                        <h3>{{ $purchase->item->name }}</h3>
                        <p>¥{{ number_format($purchase->item->price) }}</p>
                        <span class="status">{{ $purchase->item->is_sold ? 'Sold' : '' }}</span>
                    </div>
                @else
                    <p>購入した商品データが見つかりません。</p>
                @endif
            @endforeach
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('js/mypage.js') }}"></script>
@endsection