<?php

namespace Tests\Feature\Purchase;

use App\Models\Item;
use App\Models\Purchase;
use App\Models\User;
use App\Models\Address;
use App\Models\Exhibition; // 追加
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductSoldLabelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Stripe通信をモック（必要に応じて）
        \Stripe\Stripe::setApiKey('sk_test_dummy');
    }

    /** @test */
    public function user_can_complete_a_purchase_and_see_it_in_database()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // ユーザーの住所を作成
        Address::factory()->create(['user_id' => $user->id]);

        // 購入前の商品を作成
        $item = Item::factory()->create();

        // 購入処理（POSTルートを叩く）
        $response = $this->post(route('purchase.store', $item->id), [
            'payment' => 'credit',
        ]);

        $item->refresh();

        // 購入レコードを作成（支払い完了にする）
        $purchase = Purchase::where('item_id', $item->id)->first();
        $purchase->payment_status = 'completed';
        $purchase->save();

        // 購入が保存されているか確認
        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_method' => 'credit',
            'payment_status' => 'completed',
        ]);
    }

    /** @test */
    public function sold_item_displays_sold_label_in_index_view()
    {
        // ログインユーザーを作成
        $user = User::factory()->create();
        $this->actingAs($user);

        // 商品の出品者となる別のユーザーを作成
        $seller = User::factory()->create();

        // 商品を作成し、出品者と紐付ける
        $item = Item::factory()->create();
        Exhibition::factory()->create([
            'user_id' => $seller->id, // 出品者を設定
            'item_id' => $item->id,
        ]);

        // 購入レコードを作成（支払い完了にする）
        Purchase::factory()->create([
            'item_id' => $item->id,
            'user_id' => $user->id, // ログインユーザーが購入
            'payment_method' => 'credit',
            'payment_status' => 'completed',
        ]);

        // 商品一覧ページを表示（コントローラ経由）
        // おすすめタブで、かつ検索クエリがない状態でアクセス
        $response = $this->get(route('items.index', ['tab' => 'recommend']));

        $response->assertStatus(200);
        $response->assertSee('<p class="item-sold">Sold</p>', false); // Raw string match
    }
}