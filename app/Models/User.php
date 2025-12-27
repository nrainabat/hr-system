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
        'status',
        'department',    // New Field
        'position',      // New Field
        'profile_image', // New Field
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    // THE FUNCTION "getAuthIdentifierName" HAS BEEN REMOVED.
    // This allows Laravel to use the numeric 'id' correctly.
}