<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Island extends Model
{
    protected $fillable = [
        'name',
        'type',        // e.g., 'theme_park' or 'picnic'
        'description',
        'latitude',
        'longitude',
        'images',
    ];
}
