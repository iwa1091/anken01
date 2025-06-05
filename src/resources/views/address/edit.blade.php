@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/address-edit.css') }}">
@endsection

@section('content')
<div class="container">
    <h2>住所の変更</h2>

    <form method="POST" action="{{ route('address.update', ['item_id' => $item_id]) }}" novalidate>
        @csrf
        @method('PUT')

        <!-- 郵便番号 -->
        <div class="form-group">
            <label for="postal_code">郵便番号</label>
            <input type="text" name="postal_code" id="postal_code" class="form-control" value="{{ old('postal_code', $user->address->postal_code ?? '') }}" required>
            @error('postal_code')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <!-- 住所 -->
        <div class="form-group">
            <label for="address">住所</label>
            <input type="text" name="address" id="address" class="form-control" value="{{ old('address', $user->address->address ?? '') }}" required>
            @error('address')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <!-- 建物名（任意） -->
        <div class="form-group">
            <label for="building_name">建物名</label>
            <input type="text" name="building_name" id="building_name" class="form-control" value="{{ old('building_name', $user->address->building_name ?? '') }}">
        </div>

        <!-- 住所変更ボタン -->
        <button type="submit" class="btn btn-success">更新する</button>
    </form>
</div>
@endsection