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
}
