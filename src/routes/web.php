<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;
use Laravel\Fortify\Http\Controllers\PasswordResetLinkController;
use Laravel\Fortify\Http\Controllers\NewPasswordController;
use App\Http\Controllers\ExhibitionController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\VerifyEmailController;
use Laravel\Fortify\Http\Controllers\ResendVerificationEmailController;

// ----------------------------------------------------
// メール認証ルート
// ----------------------------------------------------
Route::get('/email/verify/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['auth', 'signed'])
    ->name('verification.verify');

Route::post('/email/verification-notification', function () {
    if (auth()->user()->hasVerifiedEmail()) {
        return redirect()->route('profile.edit');
    }
    
    auth()->user()->sendEmailVerificationNotification();

    return back()->with('resent', true);
})->middleware(['auth', 'signed', 'throttle:6,1'])->name('verification.resend');

// ----------------------------------------------------
// Fortify に対応した認証関連ルート
// ----------------------------------------------------
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('/register', [RegisteredUserController::class, 'store'])->name('storeRegister');

Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.update');

// ----------------------------------------------------
// 商品関連ルート（未認証ユーザーもアクセス可）
// ----------------------------------------------------
Route::get('/', [ItemController::class, 'index'])->name('items.index');
Route::get('/items', [ItemController::class, 'index'])->name('items.top');
Route::get('/item/{item_id}', [ItemController::class, 'detail'])->name('items.detail');
//Route::get('/items/search', [ItemController::class, 'search'])->name('items.search');
Route::get('/items/recommend', [ItemController::class, 'recommend'])->name('items.recommend');

// 「マイリスト」や「いいね」「コメント」は認証ユーザー専用
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/?tab=mylist', [ItemController::class, 'index'])->name('items.mylist');
    Route::post('/item/like/{item_id}', [ItemController::class, 'like'])->name('item.like');
    Route::post('/item/comment/{item_id}', [CommentController::class, 'store'])->name('item.comment');
});

// ----------------------------------------------------
// 出品関連ルート（要ログイン）
// ----------------------------------------------------
Route::get('/sell', [ExhibitionController::class, 'create'])->middleware('auth')->name('exhibition.create');
Route::post('/sell', [ExhibitionController::class, 'store'])->middleware('auth')->name('exhibition.store');

// ----------------------------------------------------
// 購入関連ルート（要ログイン）
// ----------------------------------------------------
Route::get('/purchase/{item_id}', [PurchaseController::class, 'show'])->middleware('auth')->name('purchase.show');
Route::post('/purchase/{item_id}', [PurchaseController::class, 'store'])->middleware('auth')->name('purchase.store');
Route::get('/purchase/address/{item_id}', [AddressController::class, 'edit'])->middleware('auth')->name('address.edit');
Route::put('/purchase/address/{item_id}', [AddressController::class, 'updateAddress'])->middleware('auth')->name('address.update');

// ----------------------------------------------------
// ユーザー関連ルート（要ログイン）
// ----------------------------------------------------
Route::get('/mypage', [UserController::class, 'mypage'])->middleware(['auth','verified'])->name('mypage');
Route::get('/profile/edit', [UserController::class, 'editProfile'])->middleware(['auth', 'verified'])->name('profile.edit');
Route::put('/profile/update', [UserController::class, 'updateProfile'])->middleware('auth')->name('profile.update');

