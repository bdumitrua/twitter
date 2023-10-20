<?php

namespace App\Modules\Twitt\Models;

use Database\Factories\TwittFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Twitt extends Model
{
    use HasFactory;

    protected $fillable = [
        USER_ID,
        USER_GROUP_ID,
        'text',
        'likes_count',
        'reposts_count',
        'replies_count',
        'favorites_count',
        'is_comment',
        'commented_twitt_id',
        'is_quoute',
        'quoted_twitt_id',
        'is_repost',
        'reposted_twitt_id',
    ];

    protected static function newFactory()
    {
        return TwittFactory::new();
    }
}
