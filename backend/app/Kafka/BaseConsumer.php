<?php

namespace App\Kafka;

use Enqueue\RdKafka\RdKafkaConnectionFactory;
use Interop\Queue\Context;

abstract class BaseConsumer
{
    protected $consumer;
    protected string $topicName;

    public function __construct(string $topicName)
    {
        $this->topicName = $topicName;
        $connectionFactory = new RdKafkaConnectionFactory([
            'global' => [
                'metadata.broker.list' => config('kafka.broker_list'),
            ],
        ]);

        $context = $connectionFactory->createContext();
        $topic = $context->createTopic($this->topicName);

        $this->consumer = $context->createConsumer($topic);
    }

    public abstract function consume(): void;
}
