<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->bigIncrements('id'); // 自動インクリメントの BIGINT 型主キー
            $table->unsignedBigInteger('user_id'); // users テーブルの id への外部キー
            $table->string('postal_code', 20);  // 郵便番号
            $table->text('address');           // 住所詳細
            $table->string('building_name', 255); // 建物名など（255文字以内）
            $table->timestamps(); // created_at と updated_at

            // 外部キー設定
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');  // ユーザー削除時に住所情報も削除
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('addresses');
    }
}