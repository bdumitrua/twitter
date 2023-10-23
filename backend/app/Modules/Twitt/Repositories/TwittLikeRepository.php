<?php

namespace App\Modules\Twitt\Repositories;

use App\Modules\Twitt\Models\TwittLike;
use App\Modules\User\Events\TwittLikeEvent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TwittLikeRepository
{
    protected $twittLike;

    public function __construct(
        TwittLike $twittLike,
    ) {
        $this->twittLike = $twittLike;
    }

    protected function queryByBothIds(int $twittId, int $userId): Builder
    {
        return $this->twittLike->newQuery()->where([
            'twitt_id' => $twittId,
            'user_id' => $userId,
        ]);
    }

    public function getByUserId(int $userId): Collection
    {
        return $this->twittLike
            ->where('user_id', '=', $userId)
            ->get();
    }

    public function add(int $twittId, int $userId): void
    {
        if (empty($this->queryByBothIds($twittId, $userId)->first())) {
            $twittLike = $this->twittLike->create([
                'twitt_id' => $twittId,
                'user_id' => $userId,
            ]);

            event(new TwittLikeEvent($twittLike, true));
        }
    }

    public function remove(int $twittId, int $userId): void
    {
        $twittLike = $this->twittLike->where([
            'twitt_id' => $twittId,
            'user_id' => $userId,
        ])->first();

        if (!empty($twittLike)) {
            event(new TwittLikeEvent($twittLike, false));
            $twittLike->delete();
        }
    }
}
