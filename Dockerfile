FROM php:8.2-apache

# SQLite拡張を有効化
RUN apt-get update && apt-get install -y libsqlite3-dev \
    && docker-php-ext-install pdo_sqlite \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# MPM競合を解消（mpm_eventを無効化してmpm_preforkに統一）
RUN a2dismod mpm_event mpm_worker 2>/dev/null || true \
    && a2enmod mpm_prefork rewrite

# Apacheの設定：.htaccessを許可
RUN echo '<Directory /var/www/html>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/portfolio.conf \
    && a2enconf portfolio

# ファイルをコピー
COPY . /var/www/html/

# dataフォルダの作成とパーミッション設定
RUN mkdir -p /var/www/html/data \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/data

EXPOSE 80
