<?php

namespace App\Modules\Twitt\Models;

use App\Modules\User\Models\User;
use Database\Factories\TwittFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Twitt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_group_id',
        'text',
        'likes_count',
        'reposts_count',
        'replies_count',
        'favorites_count',
        'is_comment',
        'commented_twitt_id',
        'is_reply',
        'replied_twitt_id',
        'is_repost',
        'reposted_twitt_id',
    ];

    protected static function newFactory()
    {
        return TwittFactory::new();
    }

    // public function resolveRouteBinding($value, $field = null)
    // {
    //     return Cache::remember("twitt_base_{$value}", 60, function () use ($value, $field) {
    //         return parent::resolveRouteBinding($value, $field);
    //     });
    // }

    public function author()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
