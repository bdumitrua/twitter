<?php

namespace App\Modules\Twitt\Repositories;

use App\Modules\Twitt\Models\Twitt;
use App\Modules\Twitt\Models\TwittFavorite;
use App\Modules\Twitt\Models\TwittLike;
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
        return $this->twittLike->newQuery()
            ->where([
                'twitt_id' => $twittId,
                'user_id' => $userId,
            ]);
    }

    public function getByUserId(int $userId): Collection
    {
        return $this->twittLike->where('user_id', '=', $userId)->get();
    }

    public function add(int $twittId, int $userId): void
    {
        if (empty($this->queryByBothIds($twittId, $userId)->first()))
            $this->twittLike->create([
                'twitt_id' => $twittId,
                'user_id' => $userId,
            ]);
    }

    public function remove(int $twittId, int $userId): void
    {
        $this->twittLike
            ->where([
                'twitt_id' => $twittId,
                'user_id' => $userId,
            ])
            ->delete();
    }
}
