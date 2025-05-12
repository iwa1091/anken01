<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AddressRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Address;

class AddressController extends Controller
{
    /**
     * 住所変更画面の表示
     *
     * @param int $item_id
     * @return \Illuminate\View\View
     */
    public function edit($item_id)
    {
        $user = Auth::user();
        return view('address.edit', compact('user', 'item_id'));
    }

    /**
     * 住所情報の更新処理
     *
     * @param \Illuminate\Http\Request $request
     * @param int $item_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateAddress(Request $request, $item_id)
    {

        // ログインユーザーの住所情報を取得または作成
        $user = Auth::user();
        $address = Address::updateOrCreate(
            ['user_id' => $user->id], // ユーザーに紐づく住所を検索
            [
                'postal_code' => $request->postal_code,
                'address' => $request->address,
                'building_name' => $request->building_name,
            ]
        );

        return redirect()->route('purchase.show', ['item_id' => $item_id])
                         ->with('success', '住所情報を更新しました！');
    }
}