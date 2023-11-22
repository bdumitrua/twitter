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
            $data = json_decode($message->getBody(), true);

            if (json_last_error() === JSON_ERROR_NONE) {
                echo "Получено сообщение: " . print_r($data, true) . "topic: {$this->topicName} \n";
            } else {
                echo "Ошибка при декодировании сообщения: " . $message->getBody() . ". topic: {$this->topicName} \n";
            }

            $this->consumer->acknowledge($message);
            echo "Сообщение обработано и подтверждено. topic: {$this->topicName} \n";
        }
    }
}
