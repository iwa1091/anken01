<?php

namespace Database\Factories;

use App\Models\Purchase;
use App\Models\User;
use App\Models\Item;
use App\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseFactory extends Factory
{
    protected $model = Purchase::class;

    public function definition()
    {
        return [
            'user_id' => User::factory()->create()->id, // ランダムに生成された購入者に紐づけ
            'item_id' => Item::factory()->create()->id, // ランダムに生成された商品に紐づけ
            'address_id' => Address::factory()->create()->id, // ランダムに生成された配送先住所に紐づけ
            'payment_method' => $this->faker->randomElement(['クレジットカード', '現金']), // ランダムな支払い方法
            'payment_status' => $this->faker->randomElement(['支払い済み', '未払い']), // ランダムな支払い状況
            'total_price' => $this->faker->numberBetween(1000, 50000), // ランダムな合計金額
        ];
    }
}