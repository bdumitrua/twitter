<?php

namespace App\Console\Commands\KafkaConsumers;

use App\Kafka\Consumers\UserCreatedConsumer;
use Illuminate\Console\Command;

class KafkaConsumeCommand extends Command
{
    protected $signature = 'kafka:consume:user_created';
    protected $description = 'Consume messages from user_created topic';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info("Starting user_created consumer...");

        $consumer = new UserCreatedConsumer('user_created');
        $consumer->consume();
    }
}
