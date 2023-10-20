<?php

namespace App\Modules\Twitt\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Twitt extends Model
{
    use HasFactory;

    protected $fillable = [
        // Put your changeable fields here
    ];

    protected $hidden = [
        // The attributes that should be hidden for serialization
    ];
}
