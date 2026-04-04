FROM php:8.2-apache

# SQLite拡張を有効化
RUN apt-get update && apt-get install -y libsqlite3-dev \
    && docker-php-ext-install pdo_sqlite \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# mod_rewriteを有効化
RUN a2enmod rewrite

# Apacheのドキュメントルートをportfolioフォルダに設定
ENV APACHE_DOCUMENT_ROOT /var/www/html

# ファイルをコピー
COPY . /var/www/html/

# dataフォルダの作成とパーミッション設定
RUN mkdir -p /var/www/html/data \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/data

# Apacheの設定：.htaccessを許可
RUN echo '<Directory /var/www/html>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/portfolio.conf \
    && a2enconf portfolio

EXPOSE 80
