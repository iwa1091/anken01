@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile_edit.css') }}">
@endsection

@section('content')
<div class="container">
    <h1>プロフィール設定</h1>

    <div class="content-form">
        <!-- フォーム開始 -->
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" novalidate>
            @csrf
            @method('PUT')

            <!-- プロフィール画像 -->
            <div class="form-group">
                <div class="profile-input-wrapper">
                    @if(isset($user) && $user->profile_image)
                        <img src="{{ asset('storage/' . $user->profile_image) }}" alt="プロフィール画像" class="profile-image-preview">
                    @else
                        <div class="profile-placeholder"></div>
                    @endif
                    <!-- カスタムファイルボタン -->
                    <div class="file-select-area">
                        <label for="profile_image" class="custom-file-label">画像を選択する</label>
                        <input type="file" name="profile_image" id="profile_image" class="form-control hidden-file">
                    </div>
                </div>
                @error('profile_image')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <!-- ユーザー名 -->
            <div class="form-group">
                <label for="name">ユーザー名</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name ?? '') }}" required>
                @error('name')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <!-- 郵便番号 -->
            <div class="form-group">
                <label for="postal_code">郵便番号</label>
                <input type="text" name="postal_code" id="postal_code" class="form-control" value="{{ old('postal_code', $user->postal_code ?? '') }}">
                @error('postal_code')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <!-- 住所 -->
            <div class="form-group">
                <label for="address">住所</label>
                <input type="text" name="address" id="address" class="form-control" value="{{ old('address', $user->address ?? '') }}">
                @error('address')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <!-- 建物名 -->
            <div class="form-group">
                <label for="building_name">建物名</label>
                <input type="text" name="building_name" id="building_name" class="form-control" value="{{ old('building_name', $user->building_name ?? '') }}">
                @error('building_name')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <!-- 保存ボタン -->
            <button type="submit" class="btn btn-primary">保存</button>
        </form>
    </div>
</div>
@endsection
