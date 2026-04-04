FROM php:8.2-apache

# SQLite拡張を有効化
RUN apt-get update && apt-get install -y libsqlite3-dev \
    && docker-php-ext-install pdo_sqlite \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# MPM競合を完全解消
RUN cd /etc/apache2/mods-enabled \
    && rm -f mpm_*.load mpm_*.conf 2>/dev/null || true \
    && ln -sf ../mods-available/mpm_prefork.load mpm_prefork.load \
    && ln -sf ../mods-available/mpm_prefork.conf mpm_prefork.conf

# mod_rewrite有効化
RUN a2enmod rewrite

# .htaccess許可
RUN echo '<Directory /var/www/html>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/portfolio.conf \
    && a2enconf portfolio

# ファイルをコピー
COPY . /var/www/html/

# パーミッション設定
RUN mkdir -p /var/www/html/data \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/data

EXPOSE 80
