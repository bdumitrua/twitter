<?php

namespace App\Kafka;

use Enqueue\RdKafka\RdKafkaConnectionFactory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class KafkaProducer
{
    /**
     * @param string $topicName
     * @param mixed $messageData
     */
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

        Log::info("Sending message in {$topicName} topic", [
            'body' => $message->getBody(),
            'properties' => $message->getProperties()
        ]);
        $context->createProducer()->send($topic, $message);
    }
}
