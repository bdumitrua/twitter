<?php

namespace App\Modules\Message\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        // Put your changeable fields here
    ];

    protected $hidden = [
        // The attributes that should be hidden for serialization
    ];
}
