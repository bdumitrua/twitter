<?php

namespace App\Kafka;

use Enqueue\RdKafka\RdKafkaConnectionFactory;
use Enqueue\RdKafka\RdKafkaConsumer;
use Illuminate\Log\LogManager;
use Illuminate\Support\Facades\Log;
use Interop\Queue\Context;
use Interop\Queue\Message;

abstract class BaseConsumer
{
    protected RdKafkaConsumer $consumer;
    protected LogManager $logger;
    protected string $topicName;
    protected string $consumerGroup;

    /**
     * @param string $topicName
     * @param string $consumerGroup
     */
    public function __construct(string $topicName, string $consumerGroup)
    {
        $this->topicName = $topicName;
        $this->consumerGroup = $consumerGroup;
        $connectionFactory = new RdKafkaConnectionFactory([
            'global' => [
                'metadata.broker.list' => config('kafka.broker_list'),
                'group.id' => $this->consumerGroup,
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

    /**
     * @param Message $message
     * 
     * @return object
     */
    protected function getMessageBody(Message $message): object
    {
        $messageUUID = $message->getProperty('uuid');
        $body = json_decode($message->getBody(), true);
        $this->logger->info("Consumer-группой {$this->consumerGroup} получено сообщение: {$messageUUID}", $body);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->logger->info(
                "В consumer-группе {$this->consumerGroup} произошла ошибка при декодировании сообщения: {$messageUUID}",
                $body
            );
            return (object)[];
        }

        return (object)$body;
    }

    /**
     * @param Message $message
     * 
     * @return void
     */
    protected function acknowledge(Message $message): void
    {
        $messageUUID = $message->getProperty('uuid');
        $this->consumer->acknowledge($message);
        $this->logger->info("Consumer-группой {$this->consumerGroup} обработано и подтверждено cообщение: {$messageUUID}");
    }
}
