<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class Teacher extends Model
{
    use HasFactory;
    protected $fillable = [
        'university_id',
        'academic_year',
        'total_teachers',
    ];
    public function university()
    {
        return $this->belongsTo(University::class);
    }
}
