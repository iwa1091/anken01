# anken01

## 環境構築

**Dockerビルド**
1. `git clone git@github.com:<your-repository-url>.git`
2. Docker Desktopアプリを立ち上げる
3. `docker-compose up -d --build`

> *MacのM1・M2チップのPCの場合、`no matching manifest for linux/arm64/v8 in the manifest list entries`のメッセージが表示されビルドができないことがあります。
エラーが発生する場合は、docker-compose.ymlファイルの「mysql」内に「platform」の項目を追加で記載してください*

```bash
mysql:
    platform: linux/x86_64
    image: mysql:8.0.26
    environment:


 **環境変数**
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass

**実行するコマンド**
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan storage:link

#使用技術(実行環境)
PHP8.3.0

Laravel8.83.27

MySQL8.0.26

#URL
開発環境：http://localhost/

phpMyAdmin：http://localhost:8080/