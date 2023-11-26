<?php

namespace App\Console\Commands\KafkaConsumers;

use App\Modules\Notification\Consumers\NewUserNotifyConsumer;
use Illuminate\Console\Command;

class NewUserNotifyConsumerCommand extends Command
{
    protected $topic = 'new_users';
    protected $signature = 'kafka:consume:new_user_notify';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            $this->info("Starting " . NewUserNotifyConsumer::class . "...");

            $consumer = new NewUserNotifyConsumer($this->topic, NewUserNotifyConsumer::class);
            $consumer->consume();
        } catch (\LogicException $e) {
            $this->error($e->getMessage());
        }
    }
}
