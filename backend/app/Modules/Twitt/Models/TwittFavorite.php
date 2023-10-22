<?php

namespace App\Modules\Twitt\Models;

use App\Modules\User\Models\User;
use Database\Factories\TwittFavoriteFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwittFavorite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'twitt_id',
    ];

    protected static function newFactory()
    {
        return TwittFavoriteFactory::new();
    }
}
