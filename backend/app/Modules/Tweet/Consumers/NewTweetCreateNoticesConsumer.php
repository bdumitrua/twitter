<?php

namespace App\Modules\Tweet\Consumers;

use App\Kafka\BaseConsumer;
use App\Modules\Tweet\Models\TweetNotice;
use App\Modules\User\Models\User;
use Illuminate\Log\LogManager;

class NewTweetCreateNoticesConsumer extends BaseConsumer
{
    protected $notificationService;

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
            $newTweet = $this->getMessageBody($message);

            $this->logger->debug('sdafafsddfasafsd', (array)$newTweet);

            if (!empty($newTweet) && $newTweet->type !== 'repost') {
                try {
                    $tweetText = $newTweet->text;
                    $tweetId = $newTweet->id;
                    if (empty($tweetText)) {
                        return;
                    }

                    $words = explode(' ', $tweetText);
                    $notices = [];
                    foreach ($words as $word) {
                        if (strpos($word, '@') === 0) {
                            $cleanLink = preg_replace('/[^\w]/', '', substr($word, 1));
                            $notices[] = $cleanLink;
                        }
                    }
                    $notices = array_unique($notices);

                    $noticedUsers = User::whereIn('link', $notices)->get(['id', 'link'])->toArray();
                    $noticesData = array_map(function ($noticedUser) use ($tweetId) {
                        return [
                            'link' => $noticedUser['link'],
                            'user_id' => $noticedUser['id'],
                            'tweet_id' => $tweetId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }, $noticedUsers);

                    TweetNotice::insert($noticesData);
                } catch (\Throwable $e) {
                    $this->logger->error('Error while creating notices in NewTweetCreateNoticeConsumer: ' . $e->getMessage());
                }

                $this->acknowledge($message);
            }
        }
    }
}
