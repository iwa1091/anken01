<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name, // ランダムな名前を生成
            'email' => $this->faker->unique()->safeEmail, // 一意のメールアドレスを生成
            'email_verified_at' => now(), // メール認証の日時を現在時刻で設定
            'password' => bcrypt('password'), // 初期値のパスワードを暗号化
            'remember_token' => Str::random(10), // ランダムなトークンを生成
        ];
    }

    /**
     * 未認証ユーザーの状態を定義する状態メソッド
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}