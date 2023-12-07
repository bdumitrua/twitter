<?php

namespace App\Modules\Auth\Consumers;

use App\Kafka\BaseConsumer;
use App\Mail\ResetPasswordCodeEmail;
use Illuminate\Log\LogManager;
use Illuminate\Support\Facades\Mail;

class NewPasswordResetsConsumer extends BaseConsumer
{
    public function __construct(
        string $topicName,
        string $consumerGroup,
        LogManager $logger,
    ) {
        parent::__construct($topicName, $consumerGroup);

        $this->logger = $logger;
    }

    public function consume(): void
    {
        while (true) {
            $message = $this->consumer->receive();
            $authReset = $this->getMessageBody($message);

            if (!empty($authReset)) {
                $email = $authReset->email;
                $code = $authReset->code;

                $this->logger->info('Sending password reset code', ['email' => $email]);
                Mail::to($email)->send(new ResetPasswordCodeEmail($code));

                $this->acknowledge($message);
            }
        }
    }
}
