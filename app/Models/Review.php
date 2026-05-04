<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'name',
        'rating',
        'city',
        'message',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];
}