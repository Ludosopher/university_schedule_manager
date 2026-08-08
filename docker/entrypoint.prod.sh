#!/bin/bash

set -e

echo "Preparing Laravel storage..."

if [ -L /var/www/html/public/storage ]; then
    echo "Storage symlink already exists:"
    ls -la /var/www/html/public/storage
elif [ -e /var/www/html/public/storage ]; then
    echo "ERROR: /var/www/html/public/storage exists but is not a symlink."
    exit 1
else
    php /var/www/html/artisan storage:link
fi

echo "Storage symlink:"
ls -la /var/www/html/public/storage

echo "Starting Supervisor..."

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf