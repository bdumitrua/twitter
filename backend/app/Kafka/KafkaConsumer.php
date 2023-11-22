<?php

namespace App\Kafka;

use Enqueue\RdKafka\RdKafkaConnectionFactory;

class KafkaConsumer
{
    private $consumer;

    public function __construct()
    {
        $connectionFactory = new RdKafkaConnectionFactory([
            'global' => [
                'metadata.broker.list' => config('kafka.broker_list'),
            ],
        ]);

        $context = $connectionFactory->createContext();
        $topic = $context->createTopic('user_created');

        $this->consumer = $context->createConsumer($topic);
    }

    public function consume()
    {
        while (true) {
            $message = $this->consumer->receive();

            // Извлечение данных из сообщения
            $data = json_decode($message->getBody(), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                // Успешное декодирование JSON
                echo "Получено сообщение: " . print_r($data, true) . "\n";

                // Здесь может быть ваша логика обработки, использующая $data
                // ...
            } else {
                // Ошибка декодирования JSON
                echo "Ошибка при декодировании сообщения: " . $message->getBody() . "\n";
            }

            $this->consumer->acknowledge($message);
            echo "Сообщение обработано и подтверждено.\n";
        }
    }
}
