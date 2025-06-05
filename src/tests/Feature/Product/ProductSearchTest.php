<?php

namespace Tests\Feature\Product;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Exhibition; // Exhibitionモデルをuseに追加
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductSearchTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $otherUser; // 別のユーザーを追加
    protected $itemA;
    protected $itemB;
    protected $itemC;

    /**
     * セットアップでテスト用ユーザーと商品を作成
     */
    protected function setUp(): void
    {
        parent::setUp();

        // テストユーザー作成（ログイン用）
        $this->user = User::factory()->create(['email_verified_at' => now()]); // メール認証済みとする

        // 別のユーザーを作成（商品出品用）
        $this->otherUser = User::factory()->create(['email_verified_at' => now()]);

        // 商品作成（検索用）
        $this->itemA = Item::factory()->create([
            'name' => 'テスト商品A',
        ]);
        // itemAをotherUserが出品したと紐付ける
        Exhibition::factory()->create([
            'user_id' => $this->otherUser->id,
            'item_id' => $this->itemA->id,
        ]);

        $this->itemB = Item::factory()->create([
            'name' => 'テスト商品B',
        ]);
        // itemBをotherUserが出品したと紐付ける
        Exhibition::factory()->create([
            'user_id' => $this->otherUser->id,
            'item_id' => $this->itemB->id,
        ]);

        $this->itemC = Item::factory()->create([
            'name' => '関連商品C', // 「商品」を含まない名前に変更
        ]);
        // itemCをotherUserが出品したと紐付ける
        Exhibition::factory()->create([
            'user_id' => $this->otherUser->id,
            'item_id' => $this->itemC->id,
        ]);
    }

    /**
     * @test
     * 商品名で部分一致検索ができること
     */
    public function 商品名で部分一致検索できる()
    {
        // ログインした状態で検索を実行
        // 'query' パラメータで検索ワードを送信
        $response = $this->actingAs($this->user)
                         ->get(route('items.index', ['query' => 'テスト', 'tab' => 'recommend'])); // tab=recommendを明示的に指定

        $response->assertStatus(200);

        // 検索ワードを含む商品名が表示されることを確認
        $response->assertSee($this->itemA->name);
        $response->assertSee($this->itemB->name);

        // 検索ワードを含まない商品が表示されないことを確認
        $response->assertDontSee($this->itemC->name);

        // 「商品がありません。」というメッセージが表示されないことを確認
        // 検索結果があることを前提とするため
        $response->assertDontSee('商品がありません。');
    }

    /**
     * @test
     * マイリストページでも検索状態（キーワード）が保持されること
     */
    public function マイリストページで検索キーワードが保持されている()
    {
        $searchWord = 'テスト';

        // 検索結果ページ（おすすめタブ）にアクセスし、検索ワードがフォームのvalue属性に表示されていることを確認
        $response = $this->actingAs($this->user)
                         ->get(route('items.index', ['tab' => 'recommend', 'query' => $searchWord]));
        $response->assertStatus(200);
        // HTMLのinputタグのvalue属性をチェック
        $response->assertSee('<input class="header__form--search" type="text" name="query" placeholder="なにをお探しですか？" value="' . $searchWord . '"', false);


        // マイリストページに遷移し、検索ワードがフォームのvalue属性に保持されていることを確認
        $response = $this->actingAs($this->user)
                         ->get(route('items.index', ['tab' => 'mylist', 'query' => $searchWord]));
        $response->assertStatus(200);
        // HTMLのinputタグのvalue属性をチェック
        $response->assertSee('<input class="header__form--search" type="text" name="query" placeholder="なにをお探しですか？" value="' . $searchWord . '"', false);
    }
}
