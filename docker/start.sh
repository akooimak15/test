#!/bin/sh
set -e

echo "=== Starting php-fpm ==="
php-fpm &
FPM_PID=$!

echo "=== Checking nginx ==="
which nginx
nginx -v

echo "=== Testing nginx config ==="
nginx -t

echo "=== Starting nginx ==="
exec nginx -g 'daemon off;'
