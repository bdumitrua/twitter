<?php

namespace App\Kafka\Consumers;

use App\Kafka\BaseConsumer;
use Enqueue\RdKafka\RdKafkaConnectionFactory;

class UserCreatedConsumer extends BaseConsumer
{
    public function consume(): void
    {
        while (true) {
            $message = $this->consumer->receive();

            // Извлечение данных из сообщения
            $data = json_decode($message->getBody(), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                // Успешное декодирование JSON
                echo "Получено сообщение: " . print_r($data, true) . ". topic: user_created\n";
            } else {
                // Ошибка декодирования JSON
                echo "Ошибка при декодировании сообщения: " . $message->getBody() . ". topic: user_created\n";
            }

            $this->consumer->acknowledge($message);
            echo "Сообщение обработано и подтверждено. topic: user_created\n";
        }
    }
}
