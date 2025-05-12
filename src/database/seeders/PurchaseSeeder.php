<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Purchase;

class PurchaseSeeder extends Seeder
{
    public function run()
    {
        // 20件の購入データを生成
        Purchase::factory()->count(20)->create();
    }
}