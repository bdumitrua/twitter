<?php

namespace App\Modules\User\Models;

use App\Prometheus\PrometheusServiceProxy;
use Database\Factories\UsersListMemberFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * * Модель, относящаяся к таблице users_list_members
 * 
 * * Необходима для хранения списка отслеживаемых пользователей (мемберов).
 */
class UsersListMember extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'users_list_id'
    ];

    protected static function newFactory()
    {
        return UsersListMemberFactory::new();
    }

    /**
     * @return BelongsTo
     */
    public function listsData(): BelongsTo
    {
        return $this->belongsTo(UsersList::class, 'users_list_id');
    }

    /**
     * @return BelongsTo
     */
    public function usersData(): BelongsTo
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
            app(PrometheusServiceProxy::class)->incrementEntityCreatedCount('UsersListMember');
        });
    }
}
