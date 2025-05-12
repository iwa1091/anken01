<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\User;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition()
    {
        return [
            'item_id' => Item::factory()->create()->id, // ランダムに生成された商品に紐づけ
            'user_id' => User::factory()->create()->id, // ランダムに生成されたユーザーに紐づけ
            'content' => $this->faker->realText(50), // ランダムに50文字程度のコメントを生成
        ];
    }
}