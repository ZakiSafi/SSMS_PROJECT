<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;
    protected $fillable = [
        'university_id',
        'email',
        'phone',
        'address',
        'image',
    ];

    public function university()
    {
        return $this->belongsTo(University::class);
    }

}
