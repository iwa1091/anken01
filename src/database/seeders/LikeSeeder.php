<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Like;

class LikeSeeder extends Seeder
{
    public function run()
    {
        // 20件の「いいね」データを生成
        Like::factory()->count(20)->create();
    }
}