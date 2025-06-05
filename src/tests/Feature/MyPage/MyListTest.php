<?php

namespace Tests\Feature\MyPage;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Like;
use App\Models\Purchase; // 購入済み商品のテストのために追加
use App\Models\Exhibition; // 出品商品のテストのために追加
use Illuminate\Foundation\Testing\RefreshDatabase;

class MyListTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * 認証済みユーザーがマイリストタブでいいねした商品のみを表示できることをテストする。
     * 要件: いいねした商品だけが表示される
     * 1. ユーザーにログインをする
     * 2. マイリストページを開く
     * 3. いいねをした商品が表示される
     */
    public function verified_user_can_view_only_liked_items_in_mylist_tab(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]); // メール認証済みユーザー
        $likedItem = Item::factory()->create(); // いいねする商品
        $notLikedItem = Item::factory()->create(); // いいねしない商品

        // いいねを作成
        Like::create([
            'user_id' => $user->id,
            'item_id' => $likedItem->id,
        ]);

        // マイリストタブをリクエスト
        $response = $this->actingAs($user)->get('/items?tab=mylist');

        $response->assertStatus(200);
        // いいねした商品が含まれており、いいねしていない商品が含まれていないことを確認
        $response->assertViewHas('items', function ($items) use ($likedItem, $notLikedItem) {
            return $items->contains('id', $likedItem->id) && !$items->contains('id', $notLikedItem->id);
        });
        // ページ内にいいねした商品名が表示され、いいねしていない商品名が表示されないことを確認
        $response->assertSee($likedItem->name);
        $response->assertDontSee($notLikedItem->name);
    }

    /**
     * @test
     * 認証済みユーザーがマイリストで、購入済みの商品に「Sold」ラベルが表示されることをテストする。
     * 要件: 購入済み商品は「Sold」と表示される
     * 1. ユーザーにログインをする
     * 2. マイリストページを開く
     * 3. 購入済み商品を確認する
     * 4. 購入済み商品に「Sold」のラベルが表示される
     */
    public function verified_user_sees_sold_label_on_purchased_items_in_mylist(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]); // メール認証済みユーザー
        $seller = User::factory()->create(); // 商品を出品する別のユーザー
        $item = Item::factory()->create();

        // ユーザーがいいねする
        Like::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // アイテムを出品者と関連付ける
        Exhibition::factory()->create([
            'user_id' => $seller->id,
            'item_id' => $item->id,
        ]);

        // アイテムを「購入済み」にする
        // 購入にはAddressが必要なので、事前に作成
        $address = \App\Models\Address::factory()->create(['user_id' => $user->id]);
        Purchase::create([
            'user_id' => $user->id, // いいねしたユーザーが購入したと仮定
            'item_id' => $item->id,
            'address_id' => $address->id, // 関連する住所のIDを設定
            'payment_method' => 'credit',
            'payment_status' => 'completed',
            'total_price' => $item->price,
        ]);

        // マイリストタブをリクエスト
        $response = $this->actingAs($user)->get('/items?tab=mylist');

        $response->assertStatus(200);
        $response->assertSee($item->name);
        // 「Sold」ラベルが表示されることを確認 (HTML構造に合わせて調整してください)
        // 例: Bladeテンプレートで `<p class="item-sold">Sold</p>` のように表示されている場合
        $response->assertSee('<p class="item-sold">Sold</p>', false);
        // または、単にテキスト「Sold」が表示されることを確認
        // $response->assertSee('Sold');
    }

    /**
     * @test
     * 認証済みユーザーがマイリストで、自分が出品した商品が表示されないことをテストする。
     * 要件: 自分が出品した商品は表示されない
     * 1. ユーザーにログインをする
     * 2. マイリストページを開く
     * 3. 自分が出品した商品が一覧に表示されない
     */
    public function verified_user_does_not_see_their_own_exhibited_items_in_mylist(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]); // メール認証済みユーザー
        $this->actingAs($user); // ログイン

        $ownExhibitedItem = Item::factory()->create(['name' => '自分の出品商品']); // ユーザー自身が出品する商品
        $otherUserLikedItem = Item::factory()->create(['name' => '他のユーザーがいいねした商品']); // 他のユーザーが出品し、このユーザーがいいねする商品

        // ユーザーが自分が出品した商品を登録
        Exhibition::factory()->create([
            'user_id' => $user->id,
            'item_id' => $ownExhibitedItem->id,
        ]);
        // ユーザーが自分の出品商品にいいねする（マイリストに表示される可能性のある条件）
        Like::create([
            'user_id' => $user->id,
            'item_id' => $ownExhibitedItem->id,
        ]);

        // ユーザーが他の商品にいいねする
        Exhibition::factory()->create([
            'user_id' => User::factory()->create()->id, // 別の出品者
            'item_id' => $otherUserLikedItem->id,
        ]);
        Like::create([
            'user_id' => $user->id,
            'item_id' => $otherUserLikedItem->id,
        ]);

        // マイリストタブをリクエスト
        $response = $this->get('/items?tab=mylist');

        $response->assertStatus(200);
        // いいねした他の商品は表示されることを確認
        $response->assertSee($otherUserLikedItem->name);
        // 自分が出品した商品が含まれていないことを確認（ビューに渡される$itemsコレクションで）
        $response->assertViewHas('items', function ($items) use ($ownExhibitedItem) {
            return !$items->contains('id', $ownExhibitedItem->id);
        });
        // ページ内に自分が出品した商品名が表示されないことを確認
        $response->assertDontSee($ownExhibitedItem->name);
    }

    /**
     * @test
     * 未認証ユーザー（メール認証が完了していないユーザー）がマイリストタブにアクセスした際、
     * 空のコレクションが返され、商品が表示されないことをテストする。
     * 要件: 未認証の場合は何も表示されない
     * 1. ユーザーにログインをする（メール未認証）
     * 2. マイリストページを開く
     * 3. 何も表示されない
     */
    public function unverified_user_gets_empty_collection_in_mylist(): void
    {
        $user = User::factory()->create(['email_verified_at' => null]); // メール未認証ユーザー
        $likedItem = Item::factory()->create();

        // 未認証ユーザーがいいねしても、マイリストには表示されないはず
        Like::create([
            'user_id' => $user->id,
            'item_id' => $likedItem->id,
        ]);

        // マイリストタブをリクエスト
        $response = $this->actingAs($user)->get('/items?tab=mylist');

        $response->assertStatus(200);
        $response->assertViewHas('items', function ($items) {
            return $items->isEmpty(); // itemsコレクションが空であることを確認
        });
        // ページに商品名が表示されないことを確認
        $response->assertDontSee($likedItem->name);
        // 「商品がありません。」のようなメッセージが表示されることを確認しても良い
        // $response->assertSee('商品がありません。');
    }

    /**
     * @test
     * ゲストユーザー（未ログインユーザー）がマイリストタブにアクセスした際、
     * 空のコレクションが返され、商品が表示されないことをテストする。
     * 要件: 未認証の場合は何も表示されない (ゲストも含む)
     * 1. ユーザーにログインしない（ゲスト）
     * 2. マイリストページを開く
     * 3. 何も表示されない
     */
    public function guest_gets_empty_collection_in_mylist(): void
    {
        $item = Item::factory()->create(); // ゲストはいいねできないが、念のため商品を作成

        // マイリストタブをリクエスト（未ログイン状態）
        $response = $this->get('/items?tab=mylist');

        $response->assertStatus(200);
        $response->assertViewHas('items', function ($items) {
            return $items->isEmpty(); // itemsコレクションが空であることを確認
        });
        // ページに商品名が表示されないことを確認
        $response->assertDontSee($item->name);
        // 「商品がありません。」のようなメッセージが表示されることを確認しても良い
        // $response->assertSee('商品がありません。');
    }

    /**
     * @test
     * いいねの作成と解除が正しく行われ、リダイレクトされることをテストする。
     */
    public function like_creates_and_deletes_like_and_redirects(): void
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // いいねしていない状態から、いいねする
        $response = $this->actingAs($user)->post("/item/like/{$item->id}");
        $response->assertRedirect(); // リダイレクトを確認
        $response->assertSessionHas('status', 'いいねしました'); // セッションメッセージの確認
        $this->assertDatabaseHas('likes', [ // データベースにいいねが追加されたことを確認
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // いいねしている状態から、いいね解除する
        $response2 = $this->actingAs($user)->post("/item/like/{$item->id}");
        $response2->assertRedirect(); // リダイレクトを確認
        $response2->assertSessionHas('status', 'いいねを解除しました'); // セッションメッセージの確認
        $this->assertDatabaseMissing('likes', [ // データベースからいいねが削除されたことを確認
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }
}
