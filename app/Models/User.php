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
        'department',    
        'position',     
        'profile_image', 
        'phone_number',
        'gender',
        'about',
        'address',
        'supervisor_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function subordinates()
    {
        return $this->hasMany(User::class, 'supervisor_id');
    }
}