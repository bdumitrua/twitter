<?php

namespace App\Modules\User\Models;

use App\Prometheus\PrometheusService;
use Database\Factories\UserGroupMemberFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

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
    public function users_data(): BelongsTo
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
            app(PrometheusService::class)->incrementEntityCreatedCount('UserGroupMember');
        });
    }
}
