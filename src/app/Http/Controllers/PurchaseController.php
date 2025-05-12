<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseRequest;
use App\Models\Purchase;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function show($item_id)
    {
        $item = Item::findOrFail($item_id);
        $purchase = Purchase::where('item_id', $item_id)
                            ->where('user_id', Auth::id())
                            ->first();

        return view('purchase.show', compact('item', 'purchase', 'item_id'));
    }

    public function store(PurchaseRequest $request, $item_id)
    {
        $item = Item::findOrFail($item_id);
        $address = Auth::user()->addresses()->latest()->first();

        if (!$address) {
            return redirect()->back()->withErrors(['address' => '住所情報が設定されていません。']);
        }

        DB::transaction(function () use ($item, $address, $request) {
            Purchase::create([
                'user_id' => Auth::id(),
                'item_id' => $item->id,
                'address_id' => $address->id,
                'payment_method' => strtolower($request->payment),
                'payment_status' => 'pending',
                'total_price' => $item->price + ($item->shipping_fee ?? 0) - ($item->discount ?? 0),
            ]);
        });

        return redirect()->route('mypage.profile')->with('success', '商品を購入しました！');
    }
}