@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endsection

@section('content')
<div class="container">

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

    <!-- タブ切り替え -->
    <div class="tab-header">
        <a href="{{ route('mypage', ['tab' => 'listed']) }}"
           class="tab {{ $activeTab === 'listed' ? 'active' : '' }}">出品した商品</a>

        <a href="{{ route('mypage', ['tab' => 'purchases']) }}"
           class="tab {{ $activeTab === 'purchases' ? 'active' : '' }}">購入した商品</a>
    </div>

    <div class="line"></div>

    <!-- 出品商品 -->
    @if($activeTab === 'listed')
    <div class="tab-content">
        <div class="item-list">
            @forelse($listed as $exhibition)
                @if(isset($exhibition->item))
                    <div class="item">
                        <img src="{{ asset('storage/' . $exhibition->item->img_url) }}" alt="{{ $exhibition->item->name }}" class="item-image">
                        <h3>{{ $exhibition->item->name }}</h3>
                        <span class="status">{{ $exhibition->item->isSold() ? 'Sold' : '' }}</span>
                    </div>
                @endif
            @empty
                <p class="no-items">出品した商品はありません。</p>
            @endforelse
        </div>
    </div>
    @endif

    <!-- 購入商品 -->
    @if($activeTab === 'purchases')
    <div class="tab-content">
        <div class="item-list">
            @forelse($purchases as $purchase)
                @if(isset($purchase->item))
                    <div class="item">
                        <img
                            src="{{ Str::startsWith($purchase->item->img_url, 'http') ? $purchase->item->img_url : asset('storage/' . $purchase->item->img_url) }}"
                            alt="{{ $purchase->item->name }}"
                            class="item-image"
                        >
                        <h3>{{ $purchase->item->name }}</h3>
                        <span class="status">{{ $purchase->item->isSold() ? 'Sold' : '' }}</span>
                    </div>
                @endif
            @empty
                <p class="no-items">購入した商品はありません。</p>
            @endforelse
        </div>
    </div>
    @endif
</div>
@endsection
