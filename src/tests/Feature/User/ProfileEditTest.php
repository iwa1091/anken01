<?php

namespace Tests\Feature\User;

use App\Models\User;
use App\Models\Item;
use App\Models\Exhibition;
use App\Models\Category;
use App\Models\Purchase;
use App\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileEditTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ユーザー情報がプロフィールページに正しく表示される()
    {
        $user = User::factory()->create([
            'name' => 'テスト太郎',
            'profile_image' => 'test.jpg',
        ]);

        // カテゴリ作成
        $category1 = Category::factory()->create(['name' => '本']);
        $category2 = Category::factory()->create(['name' => '家電']);

        // 商品を作成し、カテゴリをattach
        $item1 = Item::factory()->create(['name' => '出品商品1', 'price' => 1000, 'description' => '説明1', 'condition' => 'good']);
        $item2 = Item::factory()->create(['name' => '出品商品2', 'price' => 2000, 'description' => '説明2', 'condition' => '中古']);
        $item1->categories()->attach($category1->id);
        $item2->categories()->attach($category2->id);

        Exhibition::factory()->create(['user_id' => $user->id, 'item_id' => $item1->id]);
        Exhibition::factory()->create(['user_id' => $user->id, 'item_id' => $item2->id]);

        $this->actingAs($user);

        $response = $this->get('/mypage?tab=listed');

        $response->assertStatus(200);
        $response->assertSee('テスト太郎');
        $response->assertSee('出品商品1');
        $response->assertSee('出品商品2');
        $response->assertSee('test.jpg');
    }

/** @test */
public function プロフィール編集画面に過去設定された初期値が表示される()
{
    $user = User::factory()->create([
        'name' => '初期名前',
    ]);

    // addresses テーブルにレコードを追加
    \App\Models\Address::factory()->create([
        'user_id' => $user->id,
        'postal_code' => '1234567', // ハイフンなしで保存
        'address' => '千葉県市原市',
    ]);

    $this->actingAs($user);

    $response = $this->get('/profile/edit');

    $response->assertStatus(200);
    $response->assertSee('初期名前');
    $response->assertSee('1234567');
    $response->assertSee('千葉県市原市');
}


    /** @test */
    public function 商品出品情報が正しく保存される()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $category = Category::factory()->create(['name' => 'アウトドア']);

        $response = $this->post('/sell', [
            'name' => 'テスト商品',
            'description' => 'これはテスト用の商品です。',
            'price' => 3000,
            'condition' => 'good',
            'category' => [$category->id],
            
            
        ]);

        $response->assertRedirect(route('mypage'));

        $this->assertDatabaseHas('items', [
            'name' => 'テスト商品',
            'description' => 'これはテスト用の商品です。',
            'price' => 3000,
            'condition' => 'good',
        ]);

        $item = Item::where('name', 'テスト商品')->first();

        $this->assertDatabaseHas('item_categories', [
            'item_id' => $item->id,
            'category_id' => $category->id,
        ]);

        $this->assertDatabaseHas('exhibitions', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }

}
