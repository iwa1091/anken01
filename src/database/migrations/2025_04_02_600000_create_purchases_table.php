<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->bigIncrements('id');  // 主キー：自動インクリメント BIGINT
            $table->unsignedBigInteger('user_id');  // users テーブルの id への外部キー
            $table->unsignedBigInteger('item_id');  // items テーブルの id への外部キー
            $table->string('payment_method', 50); // 支払い方法（50文字以内）
            $table->unsignedBigInteger('address_id');  // addresses テーブルの id への外部キー
            $table->timestamps(); // created_at と updated_at の自動生成
            $table->string('payment_status')->default('未払い'); // 支払い状況カラムを追加
            $table->integer('total_price'); // 商品の合計金額を保存するカラム

            // 外部キー制約
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');

            $table->foreign('item_id')
                  ->references('id')->on('items')
                  ->onDelete('cascade');

            $table->foreign('address_id')
                  ->references('id')->on('addresses')
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
        Schema::dropIfExists('purchases');
    }
}