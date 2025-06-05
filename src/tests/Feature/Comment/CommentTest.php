<?php

namespace Tests\Feature\Comment;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Item;
use App\Models\Comment;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_ログイン済みユーザーはコメントを送信できる()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user)
            ->post(route('item.comment', ['item_id' => $item->id]), [
                'content' => 'テストコメント',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'content' => 'テストコメント',
        ]);

        $this->assertEquals(1, Comment::where('item_id', $item->id)->count());
    }

    public function test_未ログインユーザーはコメントを送信できない()
    {
        $item = Item::factory()->create();

        $response = $this->post(route('item.comment', ['item_id' => $item->id]), [
            'content' => '未ログインのコメント',
        ]);

        $response->assertRedirect(route('login'));

        $this->assertDatabaseMissing('comments', [
            'content' => '未ログインのコメント',
        ]);
    }

    public function test_コメント未入力の場合バリデーションエラーが出る()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('item.comment', ['item_id' => $item->id]), [
                'content' => '',
            ]);

        $response->assertSessionHasErrors(['content']);
    }

    public function test_コメントが255文字を超える場合バリデーションエラーが出る()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $longComment = str_repeat('あ', 256);

        $response = $this->actingAs($user)
            ->post(route('item.comment', ['item_id' => $item->id]), [
                'content' => $longComment,
            ]);

        $response->assertSessionHasErrors(['content']);
    }
}
