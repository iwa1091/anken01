<?php

namespace Tests\Feature\Product;

use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Exhibition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductListTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 各テスト実行前に初期設定を行う
     */
    protected function setUp(): void
    {
        parent::setUp();
        // Stripe通信をモック（必要に応じて）
        // 現状のテストでは不要ですが、設定があれば残しておきます。
        // \Stripe\Stripe::setApiKey('sk_test_dummy');
    }

    /** @test */
    public function すべての商品が表示される()
    {
        // ログインは不要なケースですが、ログインしていれば出品者が除外される可能性があります。
        // このテストでは、ログインしていない状態、または出品者以外のユーザーとしてアクセスを想定します。
        // ログインユーザーを作成しても、出品者とは別のユーザーが出品した商品にすることで、
        // ログインフィルターの影響を受けにくくできます。
        $items = Item::factory()->count(3)->create();
        foreach ($items as $item) {
            Exhibition::factory()->create([
                'item_id' => $item->id,
                'user_id' => User::factory()->create()->id, // 各商品を異なるユーザーが出品
            ]);
        }

        // 商品一覧ページを「おすすめ」タブで表示する
        // Blade側でタブの切り替えロジックがある場合、明示的に指定することが重要。
        $response = $this->get(route('items.index', ['tab' => 'recommend']));

        $response->assertStatus(200); // 正常なレスポンスを確認

        // 作成したすべての商品名が表示されることをアサート
        foreach ($items as $item) {
            $response->assertSee($item->name);
        }

        // もし、商品が表示されるために `<div class="item-card">` のようなラッパーが必要なら、
        // それらの存在もアサートすると、より堅牢になります。
        // $response->assertSee('<div class="item-card">', false);
    }

    /** @test */
    public function 購入済み商品には_sold_ラベルが表示される()
    {
        $user = User::factory()->create(); // 購入者
        $this->actingAs($user); // 購入者としてログイン

        $seller = User::factory()->create(); // 出品者
        $item = Item::factory()->create();

        Exhibition::factory()->create([
            'user_id' => $seller->id, // 出品者を設定
            'item_id' => $item->id,
        ]);

        // 商品を購入済み（支払い完了）にする
        Purchase::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_status' => 'completed', // 'completed' ステータスを明示
        ]);

        // 商品一覧ページを「おすすめ」タブで表示
        $response = $this->get(route('items.index', ['tab' => 'recommend']));

        $response->assertStatus(200); // 正常なレスポンスを確認

        // HTMLの特定のタグ内に「Sold」が表示されることをアサート（厳密なマッチング）
        // Bladeテンプレートで `<p class="item-sold">Sold</p>` のように表示されている場合
        $response->assertSee('<p class="item-sold">Sold</p>', false);
        // もし単に「Sold」というテキストだけを探すなら assertSee('Sold'); でも良いですが、
        // HTML構造まで含めて確認することで、より表示の正確性を担保できます。
    }

    /** @test */
    public function 自分が出品した商品は一覧に表示されない()
    {
        $user = User::factory()->create();
        $this->actingAs($user); // ログインユーザーとしてテスト

        // ログインユーザーが出品した商品
        $myItem = Item::factory()->create(['name' => '私の出品商品']);
        Exhibition::factory()->create([
            'user_id' => $user->id, // 出品者がログインユーザー
            'item_id' => $myItem->id,
        ]);

        // 他人が出品した商品
        $otherUser = User::factory()->create();
        $otherItem = Item::factory()->create(['name' => '他の人の商品']);
        Exhibition::factory()->create([
            'user_id' => $otherUser->id, // 出品者が別のユーザー
            'item_id' => $otherItem->id,
        ]);

        // 商品一覧ページを「おすすめ」タブで表示
        $response = $this->get(route('items.index', ['tab' => 'recommend']));

        $response->assertStatus(200); // 正常なレスポンスを確認

        // 自分が出品した商品名が表示されないことをアサート
        $response->assertDontSee($myItem->name);

        // 他の人が出品した商品名が表示されることをアサート
        $response->assertSee($otherItem->name);
    }
}