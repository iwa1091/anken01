<?php

namespace Database\Factories;

use App\Models\Like;
use App\Models\User;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

class LikeFactory extends Factory
{
    protected $model = Like::class;

    public function definition()
    {
        return [
            'item_id' => Item::factory()->create()->id, // ランダムに生成された商品に紐づけ
            'user_id' => User::factory()->create()->id, // ランダムに生成されたユーザーに紐づけ
        ];
    }
}