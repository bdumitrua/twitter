#!/bin/bash

# Получаем ID контейнера
CONTAINER_ID=$(docker-compose ps -q backend)

# Копируем папку vendor на локальную машину
docker cp $CONTAINER_ID:/app/backend/vendor ./backend/
