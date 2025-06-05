<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Exhibition;
use App\Models\Item;
use App\Models\User;

class ExhibitionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 設計書の商品データ一覧
        $items = [
            [
                'name' => '腕時計',
                'price' => 15000,
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'img_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Armani+Mens+Clock.jpg',
                'condition' => '良好',
                'brand' => 'Armani', // 【追加】ブランド名
            ],
            [
                'name' => 'HDD',
                'price' => 5000,
                'description' => '高速で信頼性の高いハードディスク',
                'img_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/HDD+Hard+Disk.jpg',
                'condition' => '目立った傷や汚れなし',
                'brand' => 'Seagate', // 【追加】ブランド名
            ],
            [
                'name' => '玉ねぎ3束',
                'price' => 300,
                'description' => '新鮮な玉ねぎ3束のセット',
                'img_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/iLoveIMG+d.jpg',
                'condition' => 'やや傷や汚れあり',
                'brand' => '地場野菜', // 【追加】ブランド名
            ],
            [
                'name' => '革靴',
                'price' => 4000,
                'description' => 'クラシックなデザインの革靴',
                'img_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Leather+Shoes+Product+Photo.jpg',
                'condition' => '状態が悪い',
                'brand' => 'Church\'s', // 【追加】ブランド名
            ],
            [
                // Note: The previous request had a 'ノートPC' entry here.
                // This entry was missing in the provided code, but it's important to include a brand for all items.
                // Assuming it was intended to be here based on the ItemFactory example.
                'name' => 'ノートPC',
                'price' => 45000,
                'description' => '高性能なノートパソコン',
                'img_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Living+Room+Laptop.jpg',
                'condition' => '良好',
                'brand' => 'HP', // 【追加】ブランド名
            ],
            [
                'name' => 'マイク',
                'price' => 8000,
                'description' => '高音質のレコーディング用マイク',
                'img_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Music+Mic+4632231.jpg',
                'condition' => '目立った傷や汚れなし',
                'brand' => 'Audio-Technica', // 【追加】ブランド名
            ],
            [
                'name' => 'ショルダーバッグ',
                'price' => 3500,
                'description' => 'おしゃれなショルダーバッグ',
                'img_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Purse+fashion+pocket.jpg',
                'condition' => 'やや傷や汚れあり',
                'brand' => 'Samantha Thavasa', // 【追加】ブランド名
            ],
            [
                'name' => 'タンブラー',
                'price' => 500,
                'description' => '使いやすいタンブラー',
                'img_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Tumbler+souvenir.jpg',
                'condition' => '状態が悪い',
                'brand' => 'Zojirushi', // 【追加】ブランド名
            ],
            [
                'name' => 'コーヒーミル',
                'price' => 4000,
                'description' => '手動のコーヒーミル',
                'img_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Waitress+with+Coffee+Grinder.jpg',
                'condition' => '良好',
                'brand' => 'Kalita', // 【追加】ブランド名
            ],
            [
                'name' => 'メイクセット',
                'price' => 2500,
                'description' => '便利なメイクアップセット',
                'img_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/%E5%A4%96%E5%87%BA%E3%83%A1%E3%82%A4%E3%82%AF%E3%82%A2%E3%83%83%E3%83%95%E3%82%9A%E3%82%BB%E3%83%83%E3%83%88.jpg',
                'condition' => '目立った傷や汚れなし',
                'brand' => '資生堂', // 【追加】ブランド名
            ],
        ];

        foreach ($items as $itemData) {
            $item = Item::create($itemData); // 商品データを挿入
            Exhibition::create([
                'item_id' => $item->id,
                'user_id' => User::factory()->create()->id, // ランダムな出品者を紐づけ
            ]);
        }
    }
}
