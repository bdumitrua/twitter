<?php

namespace App\Console\Commands\KafkaConsumers;

use App\Modules\Auth\Consumers\NewRegistrationsConsumer;
use Illuminate\Console\Command;

class NewRegistrationsConsumerCommand extends Command
{
    protected $topic = 'newRegistrations';
    protected $consumerGroup = NewRegistrationsConsumer::class;
    protected $signature = 'kafka:consume:new_registration_mailing';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            $this->info("Starting {$this->consumerGroup} consumer...");

            $consumer = app()->make(NewRegistrationsConsumer::class, [
                'topicName' => $this->topic,
                'consumerGroup' => $this->consumerGroup,
            ]);

            $consumer->consume();
        } catch (\LogicException $e) {
            $this->error($e->getMessage());
        }
    }
}
