<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \App\Models\Category::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // カテゴリー名リスト
        $categories = [
            'ファッション' => 'fashion',
            '家電' => 'electronics',
            'インテリア' => 'interior',
            'レディース' => 'ladies',
            'メンズ' => 'mens',
            'コスメ' => 'cosmetics',
            '本' => 'books',
            'ゲーム' => 'games',
            'スポーツ' => 'sports',
            'キッチン' => 'kitchen',
            'ハンドメイド' => 'handmade',
            'アクセサリー' => 'accessories',
            'おもちゃ' => 'toys',
            'ベビー・キッズ' => 'baby_kids',
        ];

        // ランダムに1つのカテゴリーを返す
        $name = $this->faker->randomElement(array_keys($categories));
        $value = $categories[$name];

        return [
            'name' => $name,
            'value' => $value,
        ];
    }
}