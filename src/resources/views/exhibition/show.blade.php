@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/exhibition/show.css') }}">
@endsection

@section('content')
<div class="content">
    <div class="content__form">
        <form action="{{ route('exhibition.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <!-- 商品画像 -->
            <h1>商品の出品</h1>
            <h2 class="image">商品画像</h2>
            <div class="dashed-border">
                <label for="image" class="custom-file-upload">
                    画像を選択する
                    <input type="file" id="image" name="image" accept=".jpg, .jpeg, .png" required>
                </label>
            </div>
            <p class="contact-form__error-message">
                @error('image')
                    {{ $message }}
                @enderror
            </p>

            <!-- 商品詳細 -->
            <h2>商品の詳細</h2>
            <span class="line"></span>
            <h3 class="category">カテゴリー</h3>
            <div class="category-group">
                @foreach ($categories as $category)
                    <label class="category-label">
                        <input type="checkbox" id="category_{{ $category->id }}" name="category[]" value="{{ $category->id }}">
                        <span>{{ $category->name }}</span>
                    </label>
                @endforeach
            </div>
            <p class="contact-form__error-message">
                @error('category')
                    {{ $message }}
                @enderror
            </p>

            <label for="condition">商品の状態</label>
            <select id="condition" name="condition" required>
                <option value="" disabled selected>選択してください</option>
                <option value="excellent">良好</option>
                <option value="good">目立った傷や汚れなし</option>
                <option value="fair">やや傷や汚れあり</option>
                <option value="poor">状態が悪い</option>
            </select>
            <p class="contact-form__error-message">
                @error('condition')
                    {{ $message }}
                @enderror
            </p>

            <!-- 商品名 -->
            <h2>商品と説明</h2>
            <span class="line"></span>
            <label for="name">商品名</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required>
            <p class="contact-form__error-message">
                @error('name')
                    {{ $message }}
                @enderror
            </p>

            <!-- ブランド名 -->
            <label for="brand">ブランド名</label>
            <input type="text" id="brand" name="brand" value="{{ old('brand') }}" required>
            <p class="contact-form__error-message">
                @error('brand')
                    {{ $message }}
                @enderror
            </p>

            <!-- 商品説明 -->
            <label for="description">商品説明</label>
            <textarea id="description" name="description" required>{{ old('description') }}</textarea>
            <p class="contact-form__error-message">
                @error('description')
                    {{ $message }}
                @enderror
            </p>

            <!-- 販売価格 -->
            <label for="price">販売価格</label>
            <div class="price-wrapper">
                <span class="currency">￥</span>
                <input type="number" id="price" name="price" value="{{ old('price') }}" required>
            </div>
            <p class="contact-form__error-message">
                @error('price')
                    {{ $message }}
                @enderror
            </p>

            <button type="submit" class="form-button">出品する</button>
        </form>
    </div>
</div>
@endsection