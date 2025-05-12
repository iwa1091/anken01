<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\LoginUserRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Address;
use App\Models\Purchase;
use App\Models\Exhibition;
use Illuminate\Support\Facades\Log;


class UserController extends Controller
{
    /**
     * ログアウト処理
     */
    public function logout(Request $request)
    {
        Auth::logout(); // ユーザーをログアウト
        $request->session()->invalidate(); // セッションを無効化
        $request->session()->regenerateToken(); // セッションのトークンを再生成

        return redirect()->route('home')->with('success', 'ログアウトしました！');
    }

    /**
     * マイページの表示
     */
    public function mypage()
    {
        $user = Auth::user();

        // 購入した商品一覧の取得
        $purchasedItems = Purchase::where('user_id', $user->id)->with('item')->get();

        // 出品した商品一覧の取得
        $listedItems = Exhibition::where('user_id', Auth::id())->with('item')->get();
        //dd($listedItems);

        return view('mypage', compact('user', 'purchasedItems', 'listedItems'));
    }

    /**
     * 会員登録ページの表示
     */
    public function showRegister()
    {
        return view('auth.register'); // 会員登録ページのビューを返す
    }

    
    /**
     * プロフィール設定ページの表示
     */
    public function editProfile()
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'ログインしてください。');
        }

        return view('auth.profile_edit'); // プロフィール設定ページを表示
    }

    /**
     * プロフィール更新処理
     */
   public function updateProfile(UpdateProfileRequest $request)
    {
        Log::info('updateProfile メソッドが呼び出されました。');
        Log::info('HTTPメソッド: ' . $request->method());
        Log::info('受け取ったリクエストデータ: ' . json_encode($request->all()));

        $user = Auth::user();
        $validatedData = $request->validated();

        // プロフィール画像の保存
        if ($request->hasFile('profile_image')) {
            $validatedData['profile_image'] = $request->file('profile_image')->store('profile_images', 'public');
        }

        try {
            // ユーザー情報更新
            $user->update([
                'name' => $validatedData['name'] ?? $user->name,
                'profile_image' => $validatedData['profile_image'] ?? $user->profile_image, 
            ]);

            // `addresses` テーブルの更新
            $address = Address::where('user_id', $user->id)->first();

            if ($address) {
                $address->update([
                    'postal_code' => $validatedData['postal_code'] ?? $address->postal_code,
                    'address' => $validatedData['address'] ?? $address->address,
                    'building_name' => $validatedData['building_name'] ?? $address->building_name,
                ]);
            } else {
                Address::create([
                    'user_id' => $user->id,
                    'postal_code' => $validatedData['postal_code'],
                    'address' => $validatedData['address'],
                    'building_name' => $validatedData['building_name'],
                ]);
            }

            Log::info('ユーザー情報と住所情報が正常に更新されました。');
        } catch (\Exception $e) {
            Log::error('情報の更新中にエラーが発生しました: ' . $e->getMessage());
            return redirect()->route('item.index')->with('error', 'プロフィールの更新に失敗しました。');
        }

        Log::info('updateProfile 処理が完了しました。');
        return redirect()->route('items.index')->with('success', 'プロフィールが更新されました！');
    }
    // ログインページのビューを返す
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * ログイン処理
     */
    public function storeLogin(LoginUserRequest $request)
    {
        Log::info('storeLogin メソッドが呼び出されました！');

        $credentials = $request->validated(); //バリデーション適用

        if(Auth::attempt($credentials)) {
            Log::info('ユーザーがログイン成功！: ' .json_encode($credentials));
            $request->session()->regenerate();
            return redirect()->route('items.index')->with('success', 'ログインしました！');
        }

        Log::warning('ログイン失敗: ' . json_encode($credentials));
    } 

    
}

