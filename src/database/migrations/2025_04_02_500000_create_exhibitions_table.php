<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExhibitionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exhibitions', function (Blueprint $table) {
            $table->bigIncrements('id'); // 自動インクリメントの BIGINT 型主キー
            $table->unsignedBigInteger('user_id'); // 出品者のユーザーID（users テーブルの id を参照）
            $table->unsignedBigInteger('item_id'); // 出品される商品のID（items テーブルの id を参照）
            $table->string('status', 50)->default('active'); // 出品状態（例: active, sold 等）。初期値は active
            $table->timestamps(); // created_at と updated_at を自動管理

            // 外部キー制約の設定
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');

            $table->foreign('item_id')
                  ->references('id')->on('items')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exhibitions');
    }
}