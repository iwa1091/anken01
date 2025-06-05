<?php

namespace Tests\Feature\Purchase;

use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShippingAddressTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * 送付先住所変更画面で登録した住所が商品購入画面に正しく反映されることをテスト
     */
    public function test_shipping_address_reflects_on_purchase_page()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // 登録する住所情報
        $addressData = [
            'postal_code'   => '1234567',
            'address'       => '東京都渋谷区1-2-3',
            'building_name' => '渋谷ビル101',
        ];

        // PUTメソッドで住所を更新
        $response = $this->put(route('address.update', ['item_id' => 1]), $addressData);
        $response->assertStatus(302); // リダイレクト想定

        // 商品作成（別ユーザー）
        $seller = User::factory()->create();
        $item = Item::factory()->create([
            'name'  => 'テスト商品',
            'price' => 3000,
        ]);
        $item->exhibition()->create(['user_id' => $seller->id]);

        // 商品購入画面へアクセス
        $response = $this->get(route('purchase.show', ['item_id' => $item->id]));
        $response->assertStatus(200);

        // 登録住所が表示されているか確認
        $response->assertSee('〒123-4567');
        $response->assertSee('東京都渋谷区1-2-3');
        $response->assertSee('渋谷ビル101');
    }

    /**
     * @test
     * 購入後、購入レコードに送付先住所が正しく紐づいていることをテスト
     */
    public function test_shipping_address_attached_to_purchase()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // 住所登録（必須項目を設定）
        $address = Address::create([
            'user_id'       => $user->id,
            'postal_code'   => '9876543',
            'address'       => '大阪府大阪市4-5-6',
            'building_name' => '梅田ハイツ202',
        ]);

        $seller = User::factory()->create();
        $item = Item::factory()->create([
            'name'  => 'テスト商品2',
            'price' => 5000,
        ]);
        $item->exhibition()->create(['user_id' => $seller->id]);

        // 購入処理（支払い方法キーを正しく修正）
        $response = $this->post(route('purchase.store', ['item_id' => $item->id]), [
            'payment' => 'credit',
        ]);
        $response->assertStatus(302); // 成功でリダイレクトを期待

        // DB上の購入情報と住所の紐づけを検証
        $purchase = Purchase::where('item_id', $item->id)->first();
        $this->assertNotNull($purchase);

        // purchaseがshipping_addressリレーションを持つと仮定
        $shippingAddress =  $purchase->address;

        $this->assertEquals($address->postal_code, $shippingAddress->postal_code);
        $this->assertEquals($address->address, $shippingAddress->address);
        $this->assertEquals($address->building_name, $shippingAddress->building_name);
    }

}
