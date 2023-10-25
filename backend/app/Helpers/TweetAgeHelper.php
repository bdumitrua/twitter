<?php

namespace App\Helpers;

use App\Modules\Tweet\Models\Tweet;
use Illuminate\Support\Carbon;

class TweetAgeHelper
{
    /**
     * @param Tweet $tweet
     * @return int
     */
    public static function getTweetAge(Tweet $tweet): int
    {
        $createdAt = $tweet->created_at;
        $now = Carbon::now();

        $minutesSinceCreation = $now->diffInMinutes($createdAt);
        $hoursSinceCreation = $now->diffInHours($createdAt);
        $daysSinceCreation = $now->diffInDays($createdAt);

        if ($minutesSinceCreation <= 15) {
            return 10;
        }

        if ($hoursSinceCreation <= 1) {
            return 60;
        }

        if ($hoursSinceCreation <= 6) {
            return 5 * 60;
        }

        if ($hoursSinceCreation <= 24) {
            return 10 * 60;
        }

        if ($daysSinceCreation <= 7) {
            return 30 * 60;
        }

        // Слишком старые твитты нет смысла надолго кэшировать
        return 60;
    }
}
