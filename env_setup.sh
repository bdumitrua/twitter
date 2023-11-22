#!/bin/bash

# Чтение значений из .env файла
APP_KEY=$(awk -F '=' '/^APP_KEY=/ {print $2}' ./backend/.env)
JWT_SECRET=$(awk -F '=' '/^JWT_SECRET=/ {print $2}' ./backend/.env)

# Удаление .env файла
rm -f ./backend/.env

# Создание копии .env.example
cp ./backend/.env.example ./backend/.env
chmod 644 ./backend/.env

# Экранирование специальных символов для APP_KEY
ESCAPED_APP_KEY=$(printf '%s\n' "$APP_KEY" | sed -e 's/[\/&]/\\&/g')
ESCAPED_JWT_SECRET=$(printf '%s\n' "$JWT_SECRET" | sed -e 's/[\/&]/\\&/g')

# Замена значений в новом .env файле
sed -i '' "s|APP_KEY=.*|APP_KEY=$ESCAPED_APP_KEY|" ./backend/.env
sed -i '' "s|JWT_SECRET=.*|JWT_SECRET=$ESCAPED_JWT_SECRET|" ./backend/.env

# Удаляем composer lock чтобы актуализировать его при сборке 
rm -f ./backend/composer.lock

# Удаляем package lock чтобы актуализировать его при сборке 
rm -f ./frontend/package-lock.json
