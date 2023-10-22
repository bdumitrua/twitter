<?php

namespace App\Modules\User\Models;

use Database\Factories\UsersListSubscribtionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersListSubscribtion extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'users_list_id'
    ];

    protected static function newFactory()
    {
        return UsersListSubscribtionFactory::new();
    }

    public function lists_data()
    {
        return $this->belongsTo(UsersList::class, 'users_list_id');
    }

    public function users_data()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
