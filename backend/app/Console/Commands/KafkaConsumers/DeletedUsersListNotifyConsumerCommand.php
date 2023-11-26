<?php

namespace App\Console\Commands\KafkaConsumers;

use App\Modules\Notification\Consumers\DeletedUsersListNotifyConsumer;
use Illuminate\Console\Command;

class DeletedUsersListNotifyConsumerCommand extends Command
{
    protected $topic = 'deleted_users_lists';
    protected $signature = 'kafka:consume:deleted_users_list_notify';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            $this->info("Starting " . DeletedUsersListNotifyConsumer::class . "...");

            $consumer = new DeletedUsersListNotifyConsumer($this->topic, DeletedUsersListNotifyConsumer::class);
            $consumer->consume();
        } catch (\LogicException $e) {
            $this->error($e->getMessage());
        }
    }
}
