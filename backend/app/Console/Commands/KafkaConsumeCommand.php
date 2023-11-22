<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Kafka\KafkaConsumer;

class KafkaConsumeCommand extends Command
{
    protected $signature = 'kafka:consume';
    protected $description = 'Consume messages from Kafka';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info("Starting Kafka consumer...");

        $consumer = new KafkaConsumer();
        $consumer->consume();
    }
}
