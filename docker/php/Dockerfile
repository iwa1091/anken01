FROM php:8.3-fpm

# PHPの設定ファイルをコピー
COPY php.ini /usr/local/etc/php/

# 必要なパッケージをインストール
RUN apt update \
    && apt install -y libzip-dev unzip zlib1g-dev libpng-dev libjpeg-dev libfreetype6-dev \
    && apt install -y git curl \
    && docker-php-ext-install pdo_mysql zip \
    && apt clean

# Composerのインストール
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer \
    && composer self-update

# 作業ディレクトリを設定
WORKDIR /var/www
