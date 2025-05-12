<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition()
    {
        return [
            'user_id' => User::factory()->create()->id, // ランダムなユーザーに紐づけ
            'postal_code' => $this->faker->regexify('[0-9]{5}'), // 5桁の郵便番号のみを生成
            'address' => $this->faker->address, // ランダムな住所
            'building_name' => $this->faker->secondaryAddress, // ランダムな建物名または部屋番号
        ];
    }
}