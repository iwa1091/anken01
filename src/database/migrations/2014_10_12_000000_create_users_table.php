<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id'); // 主キー：自動インクリメントされる BIGINT 型
            $table->string('name', 255); // ユーザー名
            $table->string('email', 255)->unique(); // ユニークなメールアドレス
            $table->timestamp('email_verified_at')->nullable(); // メール認証が完了した日時。未認証の場合は NULL
            $table->string('password', 255); // パスワード
            $table->string('profile_image')->nullable(); // プロフィール画像（null許容）
            $table->rememberToken(); // Remember Me 機能用のトークン
            $table->timestamps(); // created_at と updated_at を自動生成
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}