<?php

namespace App\Modules\Search\Models;

use App\Modules\User\Models\User;
use App\Prometheus\PrometheusService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * * Модель, относящаяся к таблице recent_searches
 * 
 * * Необходима для работы с недавним поиском пользователей.
 */
class RecentSearch extends Model
{
    protected $fillable = [
        'text',
        'user_id',
        'linked_user_id'
    ];

    /**
     * @return BelongsTo
     */
    public function linkedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'linked_user_id');
    }

    /**
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        static::created(function ($model) {
            app(PrometheusService::class)->incrementEntityCreatedCount('RecentSearch');
        });
    }
}
