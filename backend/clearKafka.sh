#!/bin/bash

# Имя контейнера Kafka
kafka_container_name="kafka"

# Получение списка всех топиков
topics=$(docker-compose exec $kafka_container_name kafka-topics.sh --bootstrap-server kafka:9092 --list)

# Перебор и удаление каждого топика
for topic in $topics
do
    docker-compose exec $kafka_container_name kafka-topics.sh --bootstrap-server kafka:9092 --delete --topic $topic
done
