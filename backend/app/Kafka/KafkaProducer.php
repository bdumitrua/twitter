<?php

namespace App\Kafka;

use Enqueue\RdKafka\RdKafkaConnectionFactory;
use Illuminate\Support\Str;

class KafkaProducer
{
    public function __construct(string $topicName, $messageData)
    {
        $connectionFactory = new RdKafkaConnectionFactory([
            'global' => [
                'metadata.broker.list' => config('kafka.broker_list'),
            ],
        ]);
        $context = $connectionFactory->createContext();

        $topic = $context->createTopic($topicName);
        $message = $context->createMessage(json_encode($messageData), ['uuid' => Str::uuid()]);

        $context->createProducer()->send($topic, $message);
    }
}
