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


 **環境変数.envファイルの設定**
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass

APP_NAME=Laravel
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null

STRIPE_KEY=sk_test_xxxxxxxxxxxxxx
STRIPE_SECRET=sk_test_xxxxxxxxxxxxxx

## Forty（メール認証）の導入手順
1. `composer require fortify`
2. `php artisan vendor:publish --provider="Laravel\Fortify\FortifyServiceProvider"`
3. `php artisan migrate`
4. `.env` に以下を追加
```env
FORTIFY_AUTH_REDIRECT=/dashboard
FORTIFY_ENABLED_FEATURES=register,login


##  Stripe（決済機能）の導入手順
1. `composer require stripe/stripe-php`
2. `.env` に API キーを追加
```env
STRIPE_KEY=sk_test_xxxxxxxxxxxxxx
STRIPE_SECRET=sk_test_xxxxxxxxxxxxxx
STRIPE_WEBHOOK_SECRET=whsec_xxxxxxxxxxxxxx


**実行するコマンド**
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan storage:link

composer require stripe/stripe-php

#使用技術(実行環境)
PHP8.3.0

Laravel8.83.27

MySQL8.0.26

Nginx1.21.1

phpMyAdmin

Forty (メール認証)

Stripe （API を活用した決済機能）


#URL
開発環境：http://localhost/

phpMyAdmin：http://localhost:8080/

Mailhog:http://localhost:8025/