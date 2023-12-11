<?php

namespace App\Modules\User\Models;

use App\Prometheus\PrometheusService;
use Database\Factories\UsersListSubscribtionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * * Модель, относящаяся к таблице users_list_subscribtions
 * 
 * * Необходима для хранения списка подписчиков (читателей) списка.
 */
class UsersListSubscribtion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'users_list_id'
    ];

    protected static function newFactory()
    {
        return UsersListSubscribtionFactory::new();
    }

    /**
     * @return BelongsTo
     */
    public function lists_data(): BelongsTo
    {
        return $this->belongsTo(UsersList::class, 'users_list_id');
    }

    /**
     * @return BelongsTo
     */
    public function users_data(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        static::created(function ($model) {
            app(PrometheusService::class)->incrementEntityCreatedCount('UsersListSubscribtion');
        });
    }
}
