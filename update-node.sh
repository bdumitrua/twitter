#!/bin/bash

# Получаем ID контейнера
CONTAINER_ID=$(docker-compose ps -q frontend)

# Копируем папку vendor на локальную машину
docker cp $CONTAINER_ID:/app/frontend/node_modules ./frontend/
