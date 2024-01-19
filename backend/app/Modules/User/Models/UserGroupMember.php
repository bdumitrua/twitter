<?php

namespace App\Modules\User\Models;

use App\Prometheus\PrometheusServiceProxy;
use Database\Factories\UserGroupMemberFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * * Модель, относящаяся к таблице user_group_members
 * 
 * * Необходима для хранения списка участников (мемберов) группы.
 */
class UserGroupMember extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_group_id',
        'user_id',
    ];

    protected static function newFactory()
    {
        return UserGroupMemberFactory::new();
    }

    /**
     * @return BelongsTo
     */
    public function usersData(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        static::created(function ($model) {
            app(PrometheusServiceProxy::class)->incrementEntityCreatedCount('UserGroupMember');
        });
    }
}
