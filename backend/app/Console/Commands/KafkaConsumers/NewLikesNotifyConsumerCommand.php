<?php

namespace App\Console\Commands\KafkaConsumers;

use App\Modules\Notification\Consumers\NewLikesNotifyConsumer;
use Illuminate\Console\Command;

class NewLikesNotifyConsumerCommand extends Command
{
    protected $topic = 'new_likes';
    protected $signature = 'kafka:consume:new_likes_notify';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            $this->info("Starting " . NewLikesNotifyConsumer::class . "...");

            $consumer = new NewLikesNotifyConsumer($this->topic, NewLikesNotifyConsumer::class);
            $consumer->consume();
        } catch (\LogicException $e) {
            $this->error($e->getMessage());
        }
    }
}
