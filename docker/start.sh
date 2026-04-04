#!/bin/sh
set -e

echo "=== Starting php-fpm ==="
php-fpm &

echo "=== Waiting for php-fpm socket ==="
sleep 2

echo "=== Checking php-fpm listening ==="
netstat -tlnp 2>/dev/null | grep 9000 || ss -tlnp | grep 9000 || echo "checking with ps..."
ps aux | grep php

echo "=== Starting nginx ==="
exec nginx -g 'daemon off;'
