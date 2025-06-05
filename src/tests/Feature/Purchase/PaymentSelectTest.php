<?php

namespace Tests\Feature\Purchase;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Address; // Addressモデルを使用するため追加
use App\Models\Exhibition; // Exhibitionモデルを使用するため追加
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaymentSelectTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 各テスト実行前に初期設定を行う
     */
    protected function setUp(): void
    {
        parent::setUp();
        // Stripe通信をモック（必要に応じて）
        // このテストでは直接Stripe APIを叩くわけではないですが、
        // PurchaseControllerがStripeを使用しているため、エラーを避けるために設定します。
        \Stripe\Stripe::setApiKey('sk_test_dummy');
    }

    /**
     * ユーザーが購入を完了し、商品が売却済みと表示され、プロフィールが更新されることをテスト
     * このテストは、支払い方法の選択によるUIの即時反映ではなく、
     * 購入フロー全体の機能と、その結果が他のページに反映されることを検証します。
     *
     * @test
     */
    public function user_can_complete_purchase_and_see_sold_and_profile_updated()
    {
        // 1. ユーザー作成＆ログイン
        $user = User::factory()->create();
        $this->actingAs($user);

        // ユーザーの住所を作成（購入に必要）
        $address = Address::factory()->create(['user_id' => $user->id]);

        // 2. 出品者ユーザーと商品を作成
        $seller = User::factory()->create();
        $item = Item::factory()->create([
            // 'user_id' => $seller->id, // Itemモデルにuser_idがない場合、Exhibitionで紐付ける
            'price' => 1000,
            // 'status' => 'available', // Itemモデルにstatusカラムがない場合、Purchaseの有無で判断
        ]);

        // 商品を出品者と紐付ける（商品一覧表示のロジックに影響するため）
        Exhibition::factory()->create([
            'user_id' => $seller->id,
            'item_id' => $item->id,
        ]);

        // 3. 商品購入画面を開く（GETリクエスト）
        $response = $this->get(route('purchase.show', ['item_id' => $item->id]));
        $response->assertStatus(200); // 正常なレスポンスを確認
        $response->assertSee($item->name); // 商品名が表示されていることを確認

        // 4. 購入実行（POSTリクエスト）
        // PurchaseControllerのstoreメソッドがStripeへのリダイレクトを行うため、
        // 実際の決済処理はモックされているか、Stripeのテスト環境を使用する必要があります。
        $postData = [
            'payment' => 'credit', // クレジットカード支払いを選択
            // 'address_id' は購入画面のフォームから送られることを想定。
            // テストではユーザーに紐付く住所のIDを渡す。
            'address_id' => $address->id,
        ];

        $response = $this->post(route('purchase.store', ['item_id' => $item->id]), $postData);

        // 購入後はStripeの決済画面へリダイレクトされることを確認
        // 実際のStripeのURLは動的に変わるため、assertRedirect() で十分なことが多いです。
        $response->assertRedirect();

        // DBに購入情報が登録されていることを確認
        // PurchaseControllerのstoreメソッド内でpendingとして保存されることを想定
        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_method' => 'credit',
            'payment_status' => 'pending', // 初期状態はpendingと仮定
        ]);

        // 決済が完了したことをシミュレート（StripeからのWebhookなどを模擬）
        // 実際のアプリケーションではStripeのWebhookでstatusがcompletedに更新されます。
        $purchase = Purchase::where('item_id', $item->id)->first();
        if ($purchase) {
            $purchase->payment_status = 'completed';
            $purchase->save();
        }

        // 5. 商品が購入済み（Sold）になっているか確認
        // ItemモデルにisSold()メソッドがある場合、またはPurchaseリレーションで確認
        $item->refresh(); // DBの最新状態をロード
        // Itemモデルに'status'カラムがない場合、Purchaseの有無で判断
        // ItemモデルにisSold()メソッドがある場合、以下のように確認
        // $this->assertTrue($item->isSold());
        // または、Purchaseリレーションが存在することを確認
        $this->assertTrue($item->purchase()->exists());


        // 6. 商品一覧画面にアクセスして「Sold」が表示されるか確認
        // マイページではなく、商品一覧ページでSoldラベルが表示されることをテスト
        // ItemControllerのindexメソッドが'tab'クエリパラメータを処理する場合を考慮
        $response = $this->get(route('items.index', ['tab' => 'recommend'])); // おすすめタブでアクセス
        $response->assertStatus(200);
        // Bladeテンプレートで<p class="item-sold">Sold</p>のように表示されている場合
        $response->assertSee('<p class="item-sold">Sold</p>', false);

        // 7. プロフィール（マイページ）の購入した商品一覧に表示されるか確認
        // 一般的には'mypage'ルートが使用されます。
        $response = $this->get(route('mypage')); // マイページへのアクセス
        $response->assertStatus(200);
        $response->assertSee($item->name); // 購入した商品名が表示されていることを確認
        // マイページでもSoldラベルが表示されることを確認する場合
        $response->assertSee('Sold');
    }

    /**
     * 支払い方法選択画面でプルダウンから選択した支払い方法が正しく反映されることをテスト
     * これは、ページリロードによる表示更新を検証します。
     *
     * @test
     */
    public function payment_method_selection_reflects_on_page_load()
    {
        // ユーザー作成＆ログイン
        $user = User::factory()->create();
        $this->actingAs($user);

        // 商品を作成
        $item = Item::factory()->create(['price' => 1234]);

        // 1. 支払い方法選択画面を開く（デフォルト状態）
        $response = $this->get(route('purchase.show', ['item_id' => $item->id]));
        $response->assertStatus(200);
        // デフォルトでは「選択してください」がselectedになっていることを確認
        $response->assertSee('<option value="" disabled selected>選択してください</option>', false);
        // <p id="selected-payment">に「未選択」が表示されていることを確認
        $response->assertSee('<p id="selected-payment">', false); // タグの存在を確認
        $response->assertSee('未選択'); // テキストコンテンツの存在を確認

        // 2. プルダウンメニューから「カード支払い」を選択してページを再ロード
        $response = $this->get(route('purchase.show', [
            'item_id' => $item->id,
            'payment' => 'credit' // クエリパラメータで支払い方法を指定
        ]));
        $response->assertStatus(200);
        // 「カード支払い」がselectedになっていることを確認
        $response->assertSee('<option value="credit" selected>カード支払い</option>', false);
        // <p id="selected-payment">に「カード支払い」が表示されていることを確認
        $response->assertSee('<p id="selected-payment">', false); // タグの存在を確認
        $response->assertSee('カード支払い'); // テキストコンテンツの存在を確認

        // 3. プルダウンメニューから「コンビニ支払い」を選択してページを再ロード
        $response = $this->get(route('purchase.show', [
            'item_id' => $item->id,
            'payment' => 'convenience' // クエリパラメータで支払い方法を指定
        ]));
        $response->assertStatus(200);
        // 「コンビニ支払い」がselectedになっていることを確認
        $response->assertSee('<option value="convenience" selected>コンビニ支払い</option>', false);
        // <p id="selected-payment">に「コンビニ支払い」が表示されていることを確認
        $response->assertSee('<p id="selected-payment">', false); // タグの存在を確認
        $response->assertSee('コンビニ支払い'); // テキストコンテンツの存在を確認
    }
}
