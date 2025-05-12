<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->bigIncrements('id');            // 自動インクリメントの BIGINT 型主キー
            $table->string('name', 255);            // 商品名
            $table->bigInteger('price');            // 商品価格（BIGINT 型）
            $table->text('description');            // 商品説明
            $table->string('img_url', 255);         // 商品画像のURL
            $table->string('condition', 255);       // 商品の状態・コンディション
            $table->timestamps();                   // created_at と updated_at の自動生成
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items');
    }
}