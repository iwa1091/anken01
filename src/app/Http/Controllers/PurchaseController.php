<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseRequest;
use App\Models\Purchase;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session as CheckoutSession;

class PurchaseController extends Controller
{
    public function show(Request $request, $item_id)
    {
        $item = Item::findOrFail($item_id);
        $purchase = Purchase::where('item_id', $item_id)
                            ->where('user_id', Auth::id())
                            ->first();

        // クエリから支払い方法取得（例：credit, convenience）
        $payment = $request->query('payment');

        return view('purchase.show', compact('item', 'purchase', 'item_id', 'payment'));
    }

    public function store(Request $request, $item_id)
    {
        // バリデーション
        $request->validate([
            'payment' => 'required|in:credit,convenience',
        ]);

        // 商品取得
        $item = Item::findOrFail($item_id);
        $address = Auth::user()->addresses()->latest()->first();

        if (!$address) {
            return redirect()->back()->withErrors(['address' => '住所情報が設定されていません。']);
        }

        // Stripe APIキー設定
        Stripe::setApiKey(env('STRIPE_SECRET'));

        // 支払い方法のマッピング
        $method = $request->payment === 'credit' ? 'card' : 'konbini';

        // Stripe セッション作成
        $session = CheckoutSession::create([
            'payment_method_types' => [$method],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'unit_amount' => $item->price,
                    'product_data' => [
                        'name' => $item->name,
                    ],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => url('/payment-success'),
            'cancel_url' => url('/payment-cancel'),
        ]);

        // 購入情報を仮登録（支払いステータス pending）
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

        return redirect($session->url); // Stripe の決済画面へ遷移
    }
}
