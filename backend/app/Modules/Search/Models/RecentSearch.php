<?php

namespace App\Modules\Search\Models;

use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecentSearch extends Model
{
    protected $fillable = [
        'text',
        'user_id',
        'linked_user_id'
    ];

    public function linked_user()
    {
        return $this->belongsTo(User::class, 'linked_user_id');
    }
}
