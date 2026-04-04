FROM php:8.2-fpm-alpine

# SQLite + nginx インストール
RUN apk add --no-cache nginx sqlite-dev \
    && docker-php-ext-install pdo_sqlite

# nginx設定
RUN mkdir -p /run/nginx
COPY docker/nginx.conf /etc/nginx/nginx.conf

# ファイルをコピー
COPY . /var/www/html/

# パーミッション設定
RUN mkdir -p /var/www/html/data \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/data

# 起動スクリプト
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 80
CMD ["/start.sh"]
