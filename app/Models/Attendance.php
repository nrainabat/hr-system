<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    // 1. Table Name
    protected $table = 'attendance'; 

    protected $fillable = [
        'user_id',
        'date',
        'clock_in',
        'clock_out',
        'status',
    ];

    // 2. THIS IS THE MISSING PART causing the error
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}