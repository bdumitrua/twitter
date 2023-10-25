#!/bin/bash

# Запускаем Supervisor в фоновом режиме
# /usr/bin/supervisord -c /etc/supervisor/supervisord.conf &

# Запускаем artisan serve
php artisan serve --host=0.0.0.0
