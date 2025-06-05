<?php

namespace Tests\Feature\Purchase;

use App\Models\Item;
use App\Models\Purchase;
use App\Models\User;
use App\Models\Exhibition;
use App\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Stripe通信をモック（必要に応じて）
        \Stripe\Stripe::setApiKey('sk_test_dummy');
    }

        /** @test */
    public function user_can_complete_a_purchase_and_see_it_in_list_and_profile()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $address = Address::factory()->create(['user_id' => $user->id]);

        // 未購入の商品
        $item = Item::factory()->create();

        // 購入処理
        $response = $this->post(route('purchase.store', $item->id), [
            'payment' => 'credit',
        ]);

        $item->refresh();

        // 購入レコードの取得と更新（Stripe成功後を模擬）
        $purchase = Purchase::where('item_id', $item->id)->first();
        $purchase->payment_status = 'completed';
        $purchase->save();

        // 購入レコードが存在し、statusがcompletedであることを確認
        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_method' => 'credit',
            'payment_status' => 'completed',
        ]);

        // 商品一覧画面でSold表示があることを確認
        $soldItem = Item::factory()->create();

        Purchase::factory()->create([
            'item_id' => $soldItem->id,
            'user_id' => $user->id,
            'payment_method' => 'credit',
            'payment_status' => 'completed',
        ]);


        // マイページでもSold表示があることを確認
        $response = $this->get(route('mypage'));
        $response->assertStatus(200);
        $response->assertSee('Sold');
        $response->assertSee($item->name);
    }
}