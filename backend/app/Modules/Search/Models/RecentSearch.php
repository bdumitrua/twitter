<?php

namespace App\Modules\Search\Models;

use App\Modules\User\Models\User;
use App\Prometheus\PrometheusService;
use Illuminate\Database\Eloquent\Model;

class RecentSearch extends Model
{
    protected $fillable = [
        'text',
        'user_id',
        'linked_user_id'
    ];

    public function linkedUser()
    {
        return $this->belongsTo(User::class, 'linked_user_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            app(PrometheusService::class)->incrementEntityCreatedCount('RecentSearch');
        });
    }
}
