#!/bin/sh
set -e

echo "=== Starting php-fpm on 127.0.0.1:9000 ==="
# php-fpmをIPv4で強制起動
PHP_FPM_CONF=$(php-fpm -i 2>/dev/null | grep "Loaded Configuration" | awk '{print $NF}')
php-fpm -d listen=127.0.0.1:9000 &

sleep 2

echo "=== Checking port ==="
ss -tlnp | grep 9000 || netstat -tlnp | grep 9000

echo "=== Starting nginx ==="
exec nginx -g 'daemon off;'
