<?php

namespace Tests\Feature\Product;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Item;
use App\Models\Comment;
use App\Models\Category;
use App\Models\Like;
use App\Models\Exhibition;

class ProductDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_商品詳細ページに必要な情報が表示される()
    {
        // ユーザー、商品、出品情報の作成
        $user = User::factory()->create();
        $item = Item::factory()->create([
            'name' => 'テスト商品',
            'condition' => '良好',
            'brand' => 'ナイキ',
            'price' => 12345,
            'description' => 'これはテスト商品です。',
            'img_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Armani+Mens+Clock.jpg',
        ]);

        Exhibition::factory()->create([
            'item_id' => $item->id,
            'user_id' => $user->id,
        ]);

        // 複数カテゴリ作成・紐付け
        $categories = Category::factory()->count(2)->create();
        $item->categories()->attach($categories->pluck('id'));

        // コメント作成
        $comment = Comment::factory()->create([
            'item_id' => $item->id,
            'user_id' => $user->id,
            'content' => '良い商品です！',
        ]);

        // いいね作成
        Like::factory()->create([
            'item_id' => $item->id,
            'user_id' => $user->id,
        ]);

        // ログイン状態で商品詳細ページにアクセス
        $response = $this->actingAs($user)->get(route('items.detail', ['item_id' => $item->id]));

        // 商品情報の表示確認
        $response->assertStatus(200);
        $response->assertSee('テスト商品');
        $response->assertSee('良好');
        $response->assertSee('¥12,345');
        $response->assertSee('ナイキ');
        $response->assertSee('これはテスト商品です。');
        $response->assertSee('良い商品です！');
        $response->assertSee($user->name);
        $response->assertSee('<img', false);
        $response->assertSee('1');
        $response->assertSee('1');


        // カテゴリ名が全て表示されているか
        foreach ($categories as $category) {
            $response->assertSee($category->name);
        }
    }
}
