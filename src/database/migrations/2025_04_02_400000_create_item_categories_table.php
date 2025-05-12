<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_categories', function (Blueprint $table) {
            // 外部キー: items テーブルの id
            $table->unsignedBigInteger('item_id');
            // 外部キー: categories テーブルの id
            $table->unsignedBigInteger('category_id');

            // 複合主キー
            $table->primary(['item_id', 'category_id']);

            // 外部キー制約
            $table->foreign('item_id')
                  ->references('id')->on('items')
                  ->onDelete('cascade');

            $table->foreign('category_id')
                  ->references('id')->on('categories')
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
        Schema::dropIfExists('item_categories');
    }
}