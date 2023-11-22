<?php

namespace App\Console\Commands\KafkaConsumers;

use App\Kafka\Consumers\NewUserNotifyConsumer;
use Illuminate\Console\Command;

class KafkaConsumeCommand extends Command
{
    protected $signature = 'kafka:consume:new_user_notify';
    protected $description = 'Consume messages from your topic';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            $this->info("Starting " . NewUserNotifyConsumer::class . "...");

            $consumer = new NewUserNotifyConsumer('created_users', NewUserNotifyConsumer::class);
            $consumer->consume();
        } catch (\LogicException $e) {
            $this->error($e->getMessage());
        }
    }
}
