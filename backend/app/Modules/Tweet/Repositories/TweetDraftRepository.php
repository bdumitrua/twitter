<?php

namespace App\Modules\Tweet\Repositories;

use App\Modules\Tweet\Models\TweetDraft;
use App\Modules\Tweet\Models\TweetLike;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TweetDraftRepository
{
    protected $tweetDraft;

    public function __construct(
        TweetDraft $tweetDraft,
    ) {
        $this->tweetDraft = $tweetDraft;
    }

    public function getByUserId(int $userId): Collection
    {
        return $this->tweetDraft->where('user_id', $userId)->latest('updated_at')->get();
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
    }

    public function delete(array $drafts, int $authorizedUserId): void
    {
        $this->tweetDraft
            ->where('user_id', $authorizedUserId)
            ->whereIn('id', $drafts)
            ->delete();
    }
}
