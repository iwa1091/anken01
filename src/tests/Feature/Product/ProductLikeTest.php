<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Like; // Likeモデルをuseするのを忘れずに

class ProductLikeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ログインユーザーは商品にいいねできる()
    {
        $user = User::factory()->create();
        $user->markEmailAsVerified();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)->post(route('item.like', ['item_id' => $item->id]));

        $response->assertRedirect(); // リダイレクトOK
        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }

    /** @test */
    public function ログインユーザーが同じ商品にもう一度いいねすると解除される()
    {
        $user = User::factory()->create();
        $user->markEmailAsVerified();
        $item = Item::factory()->create();

        // 初回いいね
        $this->actingAs($user)->post(route('item.like', ['item_id' => $item->id]));

        // 再度いいね（= 解除）
        $this->actingAs($user)->post(route('item.like', ['item_id' => $item->id]));

        // likesテーブルから削除されていること
        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }

    /** @test */
    public function 未ログインユーザーはいいねできずログインページにリダイレクトされる()
    {
        $item = Item::factory()->create();

        $response = $this->post(route('item.like', ['item_id' => $item->id]));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function いいねアイコンの色が正しく変化する()
    {
        $user = User::factory()->create();
        $user->markEmailAsVerified();
        $item = Item::factory()->create();

        // 1. いいね前の状態: likedクラスがないことを確認
        $response = $this->actingAs($user)->get(route('items.detail', ['item_id' => $item->id]));
        $response->assertStatus(200);
        // likedクラスがないボタンが存在することを確認
        $response->assertDontSee('<button type="submit" class="like-button liked">', false);
        $response->assertSee('<button type="submit" class="like-button ">', false); // 空のクラスも含む形

        // 2. いいねを実行
        $this->actingAs($user)->post(route('item.like', ['item_id' => $item->id]));

        // likesテーブルにいいねが登録されていることを確認
        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // 3. いいね後の状態: likedクラスが存在することを確認
        $response = $this->actingAs($user)->get(route('items.detail', ['item_id' => $item->id]));
        $response->assertStatus(200);
        $response->assertSee('<button type="submit" class="like-button liked">', false);

        // 4. いいね解除を実行
        $this->actingAs($user)->post(route('item.like', ['item_id' => $item->id]));

        // likesテーブルからいいねが削除されていることを確認
        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // 5. いいね解除後の状態: likedクラスが再び存在しないことを確認
        $response = $this->actingAs($user)->get(route('items.detail', ['item_id' => $item->id]));
        $response->assertStatus(200);
        $response->assertDontSee('<button type="submit" class="like-button liked">', false);
        $response->assertSee('<button type="submit" class="like-button ">', false); // 空のクラスも含む形
    }

    /** @test */
    public function いいね合計値が正しく表示される()
    {
        $user1 = User::factory()->create();
        $user1->markEmailAsVerified();
        $user2 = User::factory()->create();
        $user2->markEmailAsVerified();
        $item = Item::factory()->create();

        // いいねがない初期状態
        $response = $this->actingAs($user1)->get(route('items.detail', ['item_id' => $item->id]));
        $response->assertStatus(200);
        $response->assertSee('<span class="like-count">0</span>', false);

        // user1がいいねする
        $this->actingAs($user1)->post(route('item.like', ['item_id' => $item->id]));

        // いいね後のページでカウントが1になっていることを確認
        $response = $this->actingAs($user1)->get(route('items.detail', ['item_id' => $item->id]));
        $response->assertStatus(200);
        $response->assertSee('<span class="like-count">1</span>', false);

        // user2がいいねする
        $this->actingAs($user2)->post(route('item.like', ['item_id' => $item->id]));

        // いいね後のページでカウントが2になっていることを確認 (user1としてアクセス)
        $response = $this->actingAs($user1)->get(route('items.detail', ['item_id' => $item->id]));
        $response->assertStatus(200);
        $response->assertSee('<span class="like-count">2</span>', false);

        // user1がいいねを解除する
        $this->actingAs($user1)->post(route('item.like', ['item_id' => $item->id]));

        // いいね解除後のページでカウントが1になっていることを確認 (user2としてアクセス)
        $response = $this->actingAs($user2)->get(route('items.detail', ['item_id' => $item->id]));
        $response->assertStatus(200);
        $response->assertSee('<span class="like-count">1</span>', false);
    }
}
