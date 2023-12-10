<?php

namespace App\Modules\Auth\Consumers;

use App\Kafka\BaseConsumer;
use App\Mail\RegistrationCodeMail;
use Illuminate\Log\LogManager;
use Illuminate\Support\Facades\Mail;

class NewRegistrationsConsumer extends BaseConsumer
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
            $authRegistration = $this->getMessageBody($message);

            if (!empty($authRegistration)) {
                $email = $authRegistration->email;
                $code = $authRegistration->code;

                $this->logger->info('Sending password reset code', ['email' => $email]);
                echo "Sending password reset code {$email}";
                Mail::to($email)->send(new RegistrationCodeMail($code));

                $this->acknowledge($message);
            }
        }
    }
}
