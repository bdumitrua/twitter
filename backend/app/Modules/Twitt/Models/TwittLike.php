<?php

namespace App\Modules\Twitt\Models;

use App\Modules\User\Models\User;
use Database\Factories\TwittFactory;
use Database\Factories\TwittLikeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwittLike extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'twitt_id',
    ];

    protected static function newFactory()
    {
        return TwittLikeFactory::new();
    }
}
