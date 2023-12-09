<?php

namespace App\Modules\Tweet\Models;

use App\Prometheus\PrometheusService;
use Database\Factories\TweetDraftFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TweetDraft extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'text',
    ];

    protected static function newFactory()
    {
        return TweetDraftFactory::new();
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            app(PrometheusService::class)->incrementEntityCreatedCount('TweetDraft');
        });
    }
}
