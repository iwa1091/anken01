@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
<div class="container">
    <div class="purchase-wrapper">
        <!-- 左側：商品・支払い方法・配送先 -->
        <div class="purchase-left">
            <div class="container__top">
                <div class="container__image">
                    <img
                        src="{{ Str::startsWith($item->img_url, 'http') ? $item->img_url : asset('storage/' . $item->img_url) }}"
                        alt="{{ $item->name }}"
                        class="item-image"
                    >
                </div>
                <div class="container__detail">
                    <h3>{{ $item->name }}</h3>
                    <p>¥{{ number_format($item->price) }}</p>
                </div>
            </div>

            <div class="line"></div>

            <!-- 支払い方法選択フォーム（GET） -->
            <div class="payment-group">
                <form method="GET" action="{{ route('purchase.show', ['item_id' => $item->id]) }}">
                    <div class="payment-method">
                        <label for="payment">お支払い方法</label>
                        <select name="payment" id="payment" onchange="this.form.submit()" required>
                            <option value="" disabled {{ request('payment') === null ? 'selected' : '' }}>選択してください</option>
                            <option value="credit" {{ request('payment') == 'credit' ? 'selected' : '' }}>カード支払い</option>
                            <option value="convenience" {{ request('payment') == 'convenience' ? 'selected' : '' }}>コンビニ支払い</option>
                        </select>
                        @error('payment')
                            <p class="error-text">{{ $message }}</p>
                        @enderror
                    </div>
                </form>

                <div class="line"></div>

                <!-- 配送先情報 -->
                <div class="address-info">
                    <div class="btn-group">
                        <div>配送先</div>
                        <div class="btn-secondary">
                            <a href="{{ route('address.edit', ['item_id' => $item->id]) }}" class="btn">変更する</a>
                        </div>
                    </div>
                    <div class="address-group">
                        <p>{{ Auth::user()->addresses()->first()->formatted_postal_code ?? '未設定'}}</p>
                        <p>
                            {{ Auth::user()->addresses()->first()->address ?? '未設定' }}
                            {{ Auth::user()->addresses()->first()->building_name ?? '' }}
                        </p>
                        @error('address')
                            <p class="error-text">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- 購入実行フォーム（POST） -->
                <div class="btn-left-submit">
                    <form method="POST" action="{{ route('purchase.store', ['item_id' => $item->id]) }}">
                        @csrf
                        <input type="hidden" name="payment" value="{{ request('payment') }}">
                        @error('general_error')
                            <p class="error-text">{{ $message }}</p>
                        @enderror
                        <button type="submit" class="btn btn-success">購入する</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- 右側：購入情報 -->
        <div class="purchase-summary">
            <table class="payment-table">
                <tr class="payment-table__row">
                    <td class="payment-table__item">
                        商品代金
                        <p class="item-text">¥{{ number_format($item->price) }}</p>
                    </td>
                </tr>
                <tr class="payment-table__row">
                    <td class="payment-table__item">
                        支払い方法
                        <p id="selected-payment">
                            @if(request('payment') === 'credit') カード支払い
                            @elseif(request('payment') === 'convenience') コンビニ支払い
                            @else 未選択
                            @endif
                        </p>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
@endsection

