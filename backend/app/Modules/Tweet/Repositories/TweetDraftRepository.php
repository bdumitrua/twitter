<?php

namespace App\Modules\Tweet\Repositories;

use App\Modules\Tweet\Models\TweetDraft;
use App\Modules\Tweet\Models\TweetLike;
use App\Traits\GetCachedData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TweetDraftRepository
{
    use GetCachedData;

    protected $tweetDraft;

    public function __construct(
        TweetDraft $tweetDraft,
    ) {
        $this->tweetDraft = $tweetDraft;
    }

    public function getByUserId(int $userId): Collection
    {
        $cacheKey = KEY_USER_TWEET_DRAFTS . $userId;
        return $this->getCachedData($cacheKey, 30 * 60, function () use ($userId) {
            return $this->tweetDraft->where('user_id', $userId)
                ->latest('updated_at')->get();
        });
    }

    public function create(string $text, int $authorizedUserId): void
    {
        $sameDraft = $this->tweetDraft->where('user_id', $authorizedUserId)
            ->where('text', $text)
            ->first();

        if (empty($sameDraft)) {
            $this->tweetDraft->create([
                'text' => $text,
                'user_id' => $authorizedUserId,
            ]);
        } else {
            $sameDraft->updated_at = now();
            $sameDraft->save();
        }

        $this->clearUserDraftsCache($authorizedUserId);
    }

    public function delete(array $drafts, int $authorizedUserId): void
    {
        $this->tweetDraft
            ->where('user_id', $authorizedUserId)
            ->whereIn('id', $drafts)
            ->delete();

        $this->clearUserDraftsCache($authorizedUserId);
    }

    protected function clearUserDraftsCache(int $userId): void
    {
        $cacheKey = KEY_USER_TWEET_DRAFTS . $userId;
        $this->clearCache($cacheKey);
    }
}
