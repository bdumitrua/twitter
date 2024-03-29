<?php

namespace App\Console\Commands\KafkaConsumers;

use App\Modules\Auth\Consumers\NewPasswordResetsConsumer;
use Illuminate\Console\Command;

class NewPasswordResetsConsumerCommand extends Command
{
    protected $topic = 'passwordResets';
    protected $consumerGroup = NewPasswordResetsConsumer::class;
    protected $signature = 'kafka:consume:passwordResetMailing';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            $this->info("Starting {$this->consumerGroup} consumer...");

            $consumer = app()->make(NewPasswordResetsConsumer::class, [
                'topicName' => $this->topic,
                'consumerGroup' => $this->consumerGroup,
            ]);

            $consumer->consume();
        } catch (\LogicException $e) {
            $this->error($e->getMessage());
        }
    }
}
