<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = [
        'property_id',
        'path',
        'is_main',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
