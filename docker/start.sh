#!/bin/sh
set -e

# php-fpmをバックグラウンドで起動
php-fpm &

# nginxが使えるまで少し待つ
sleep 1

# nginxをフォアグラウンドで起動（これがメインプロセス）
exec nginx -g 'daemon off;'
