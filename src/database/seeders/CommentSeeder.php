<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Comment;

class CommentSeeder extends Seeder
{
    public function run()
    {
        // 20件のコメントデータを生成
        Comment::factory()->count(20)->create();
    }
}