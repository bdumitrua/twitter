<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'email',
        'birth_date',
    ];
}
