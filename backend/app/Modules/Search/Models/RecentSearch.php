<?php

namespace App\Modules\Search\Models;

use App\Modules\User\Models\User;
use App\Prometheus\PrometheusServiceProxy;
use Database\Factories\RecentSearchFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * * Модель, относящаяся к таблице recent_searches
 * 
 * * Необходима для работы с недавним поиском пользователей.
 */
class RecentSearch extends Model
{
    use HasFactory;

    protected $fillable = [
        'text',
        'user_id',
        'linked_user_id'
    ];

    protected static function newFactory()
    {
        return RecentSearchFactory::new();
    }

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
            app(PrometheusServiceProxy::class)->incrementEntityCreatedCount('RecentSearch');
        });
    }
}
