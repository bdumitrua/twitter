<?php

namespace App\Console\Commands\KafkaConsumers;

use App\Modules\Notification\Consumers\NewNoticeNotifyConsumer;
use Illuminate\Console\Command;

class NewNoticeNotifyConsumerCommand extends Command
{
    protected $topic = 'new_notices';
    protected $consumerGroup = NewNoticeNotifyConsumer::class;
    protected $signature = 'kafka:consume:new_notice_notify';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            $this->info("Starting {$this->consumerGroup} consumer...");

            $consumer = app()->make(NewNoticeNotifyConsumer::class, [
                'topicName' => $this->topic,
                'consumerGroup' => $this->consumerGroup,
            ]);

            $consumer->consume();
        } catch (\LogicException $e) {
            $this->error($e->getMessage());
        }
    }
}
