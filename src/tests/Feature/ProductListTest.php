<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductListTest extends TestCase
{
    use RefreshDatabase; // DBはテストごとにリセットします（必要に応じて）

    /**
     * 商品一覧が正常に取得できることをテスト
     */
    public function test_product_list_can_be_retrieved()
    {
        // ここでテスト用の商品データを作成しておく例
        \App\Models\Item::factory()->count(5)->create();


        // 商品一覧取得APIにGETリクエストを送る例
        $response = $this->getJson('/api/products'); // 実際のURLに合わせてください

        // ステータスコード200で返ってくることを確認
        $response->assertStatus(200);

        // JSON構造が想定通りか簡単に確認（例）
        $response->assertJsonStructure([
            '*' => [
                'id',
                'name',
                'price',
                'description',
                // 他必要なフィールド
            ]
        ]);
    }
}
