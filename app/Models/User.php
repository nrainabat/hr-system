<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $fillable = [
        'username',
        'name',
        'email',
        'password',
        'role',
        'status'
    ];

    // Tell Laravel to authenticate using username
    public function getAuthIdentifierName()
    {
        return 'username';
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
