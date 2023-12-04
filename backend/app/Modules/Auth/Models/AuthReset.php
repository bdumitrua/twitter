<?php

namespace App\Modules\Auth\Models;

use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Model;

class AuthReset extends Model
{
    protected $fillable = [
        'user_id',
        'code',
        'confirmed'
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
