<?php

namespace App\Modules\Auth\Models;

use Illuminate\Database\Eloquent\Model;

class AuthRegistration extends Model
{
    protected $fillable = [
        'code',
        'name',
        'email',
        'birth_date',
    ];
}
