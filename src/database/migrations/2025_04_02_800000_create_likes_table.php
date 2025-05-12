<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('likes', function (Blueprint $table) {
            $table->bigIncrements('id'); // 自動インクリメントの BIGINT 型主キー
            $table->unsignedBigInteger('item_id'); // items テーブルの id への外部キー
            $table->unsignedBigInteger('user_id'); // users テーブルの id への外部キー
            $table->timestamps();              // created_at と updated_at を追加

            // 外部キー制約の設定
            $table->foreign('item_id')
                  ->references('id')->on('items')
                  ->onDelete('cascade');

            $table->foreign('user_id')
                  ->references('id')->on('users')
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
        Schema::dropIfExists('likes');
    }
}