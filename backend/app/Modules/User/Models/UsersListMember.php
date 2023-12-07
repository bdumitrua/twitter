<?php

namespace App\Modules\User\Models;

use App\Prometheus\PrometheusService;
use Database\Factories\UsersListMemberFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    public function lists_data()
    {
        return $this->belongsTo(UsersList::class, 'users_list_id');
    }

    public function users_data()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            app(PrometheusService::class)->incrementEntityCreatedCount('UsersListMember');
        });
    }
}
