<?php

namespace App\Modules\Twitt\Repositories;

use App\Modules\Twitt\Models\TwittFavorite;
use App\Modules\User\Events\TwittFavoriteEvent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TwittFavoriteRepository
{
    protected $twittFavorite;

    public function __construct(
        TwittFavorite $twittFavorite,
    ) {
        $this->twittFavorite = $twittFavorite;
    }

    protected function queryByBothIds(int $twittId, int $userId): Builder
    {
        return $this->twittFavorite->newQuery()
            ->where([
                'twitt_id' => $twittId,
                'user_id' => $userId,
            ]);
    }

    public function getByUserId(int $userId): Collection
    {
        return $this->twittFavorite
            ->where('user_id', '=', $userId)
            ->get();
    }

    public function add(int $twittId, int $userId): void
    {
        if (empty($this->queryByBothIds($twittId, $userId)->first())) {
            $twittFavorite = $this->twittFavorite->create([
                'twitt_id' => $twittId,
                'user_id' => $userId,
            ]);

            event(new TwittFavoriteEvent($twittFavorite, true));
        }
    }

    public function remove(int $twittId, int $userId): void
    {
        $twittFavorite = $this->twittFavorite->where([
            'twitt_id' => $twittId,
            'user_id' => $userId,
        ])->first();

        if (!empty($twittFavorite)) {
            event(new TwittFavoriteEvent($twittFavorite, false));
            $twittFavorite->delete();
        }
    }
}
