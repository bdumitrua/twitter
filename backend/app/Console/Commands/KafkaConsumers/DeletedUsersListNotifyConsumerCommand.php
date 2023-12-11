<?php

namespace App\Console\Commands\KafkaConsumers;

use App\Modules\Notification\Consumers\DeletedUsersListNotifyConsumer;
use Illuminate\Console\Command;

class DeletedUsersListNotifyConsumerCommand extends Command
{
    protected $topic = 'deletedUsersLists';
    protected $consumerGroup = DeletedUsersListNotifyConsumer::class;
    protected $signature = 'kafka:consume:deletedUsersListNotify';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            $this->info("Starting {$this->consumerGroup} consumer...");

            $consumer = app()->make(DeletedUsersListNotifyConsumer::class, [
                'topicName' => $this->topic,
                'consumerGroup' => $this->consumerGroup
            ]);

            $consumer->consume();
        } catch (\LogicException $e) {
            $this->error($e->getMessage());
        }
    }
}
