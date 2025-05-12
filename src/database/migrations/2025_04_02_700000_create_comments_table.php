<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->bigIncrements('id');                       // 自動インクリメント BIGINT 主キー
            $table->unsignedBigInteger('item_id');              // items テーブルの id への参照
            $table->unsignedBigInteger('user_id');              // users テーブルの id への参照
            $table->text('content');                            // コメント内容
            $table->timestamps();                               // created_at と updated_at

            // 外部キー制約設定
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
        Schema::dropIfExists('comments');
    }
}