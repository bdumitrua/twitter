<?php

namespace App\Kafka;

use Enqueue\RdKafka\RdKafkaConnectionFactory;
use Interop\Queue\Context;
use Interop\Queue\Message;

abstract class BaseConsumer
{
    protected $consumer;
    protected string $topicName;

    public function __construct(string $topicName, string $consumerGroup)
    {
        $this->topicName = $topicName;
        $connectionFactory = new RdKafkaConnectionFactory([
            'global' => [
                'metadata.broker.list' => config('kafka.broker_list'),
                'group.id' => $consumerGroup,
                'enable.auto.commit' => 'false',
            ],
            'topic' => [
                'auto.offset.reset' => 'earliest',
            ],
        ]);

        $context = $connectionFactory->createContext();
        $topic = $context->createTopic($this->topicName);

        $this->consumer = $context->createConsumer($topic);
    }

    public abstract function consume(): void;

    protected function getMessageBody(Message $message): object
    {
        $messageUUID = $message->getProperty('uuid');
        $body = json_decode($message->getBody(), true);
        echo "В топике {$this->topicName} получено сообщение: {$messageUUID}\n" . print_r($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "В топике {$this->topicName} произошла ошибка при декодировании сообщения: {$messageUUID}\n" . $message->getBody();
            return (object)[];
        }

        return (object)$body;
    }

    protected function acknowledge($message): void
    {
        $messageUUID = $message->getProperty('uuid');
        $this->consumer->acknowledge($message);
        echo "В топике {$this->topicName} обработано и подтверждено cообщение: {$messageUUID}\n";
    }
}
