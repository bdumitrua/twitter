<?php

namespace App\Console\Commands\KafkaConsumers;

use App\Modules\Notification\Consumers\NewNoticeNotifyConsumer;
use Illuminate\Console\Command;

class NewNoticeNotifyConsumerCommand extends Command
{
    protected $topic = 'new_notices';
    protected $signature = 'kafka:consume:new_notice_notify';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            $this->info("Starting " . NewNoticeNotifyConsumer::class . "...");

            $consumer = new NewNoticeNotifyConsumer($this->topic, NewNoticeNotifyConsumer::class);
            $consumer->consume();
        } catch (\LogicException $e) {
            $this->error($e->getMessage());
        }
    }
}
