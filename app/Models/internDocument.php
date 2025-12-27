<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'filename',
        'file_path',
        'description',
        'status',
        'signed_file_path',   // <--- Added
        'supervisor_comment', // <--- Added
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}