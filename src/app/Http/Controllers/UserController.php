<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\LoginUserRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Address;
use App\Models\Purchase;
use App\Models\Exhibition;

class UserController extends Controller
{


    /**
     * マイページの表示
     */
    public function mypage(Request $request)
    {
        $user = Auth::user();

        // 購入した商品一覧の取得
        $purchases = Purchase::where('user_id', $user->id)->with('item')->get();

        // 出品した商品一覧の取得
        $listed = Exhibition::where('user_id', $user->id)->with('item')->get();

        $activeTab = $request->query('tab', 'purchases');

        return view('mypage', compact('user', 'purchases', 'listed', 'activeTab'));
    }

    /**
     * 会員登録ページの表示
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * プロフィール設定ページの表示
     */
    public function editProfile()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'ログインしてください。');
        }

            $user = Auth::user(); // 現在のログインユーザーを取得
            $address = $user->addresses()->latest()->first(); // リレーション経由で住所情報を取得

        return view('auth.profile_edit', compact('user', 'address'));
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
            // ユーザー情報の更新
            $user->update([
                'name' => $validatedData['name'] ?? $user->name,
                'profile_image' => $validatedData['profile_image'] ?? $user->profile_image,
            ]);

            // 住所情報の更新または作成
            $address = Address::firstOrNew(['user_id' => $user->id]);
            $address->fill([
                'postal_code' => $validatedData['postal_code'] ?? $address->postal_code,
                'address' => $validatedData['address'] ?? $address->address,
                'building_name' => $validatedData['building_name'] ?? $address->building_name,
            ])->save();

            Log::info('ユーザー情報と住所情報が正常に更新されました。');
        } catch (\Exception $e) {
            Log::error('情報の更新中にエラーが発生しました: ' . $e->getMessage());
            return redirect()->route('item.index')->with('error', 'プロフィールの更新に失敗しました。');
        }

        Log::info('updateProfile 処理が完了しました。');
        return redirect()->route('items.index')->with('success', 'プロフィールが更新されました！');
    }



}
