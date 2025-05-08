<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'university_id',
        'email',
        'phone',
        'address',
        'image',
    ];

}
