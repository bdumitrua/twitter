<?php

namespace App\Console\Commands\KafkaConsumers;

use App\Kafka\Consumers\UserCreatedConsumer;
use Illuminate\Console\Command;

class KafkaConsumeCommand extends Command
{
    protected $signature = 'kafka:consume {topic}';
    protected $description = 'Consume messages from your topic';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            $topic = $this->argument('topic');
            $this->info("Starting " . $topic . " topic consumer...");

            $consumer = new UserCreatedConsumer($topic);
            $consumer->consume();
        } catch (\LogicException $e) {
            $this->error($e->getMessage());
        }
    }
}
