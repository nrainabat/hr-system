<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    // 1. Update $fillable to include your new fields
    protected $fillable = [
        'username',
        'name',
        'email',
        'password',
        'role',
        'status',
        'department',    // <--- Added
        'position',      // <--- Added
        'profile_image', // <--- Added
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    // IMPORTANT: The function "getAuthIdentifierName" has been REMOVED.
    // This allows Laravel to use the correct 'id' (number) for database relationships.
}