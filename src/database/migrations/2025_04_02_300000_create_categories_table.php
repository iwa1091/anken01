<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->bigIncrements('id');       // 主キー: 自動インクリメントの BIGINT 型
            $table->string('name', 100);         // カテゴリー名（100文字以内）
            $table->string('value');            //値（フォームで使用）
            $table->timestamps();               // created_at と updated_at（必要に応じて削除可能）
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories');
    }
}