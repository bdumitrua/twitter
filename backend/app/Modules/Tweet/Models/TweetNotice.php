<?php

namespace App\Modules\Tweet\Models;

use App\Modules\User\Models\User;
use Database\Factories\TweetFavoriteFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/* 
    Т.к. упоминание можно только создать (изменить логичски нельзя, 
    а для для удаления необходимо изменить твитт (что на данный момент невозможно сделать)),
    то для него создана только таблица и модель, без путей, сервиса и т.д.
*/

class TweetNotice extends Model
{
    protected $fillable = [
        'link',
        'user_id',
        'tweet_id',
    ];
}
