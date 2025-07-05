<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Teacher extends Model
{
    use HasFactory;
    protected $fillable = [
        'university_id',
        'academic_year',
        'total_teachers',
    ];
        protected static function booted()
{
    static::addGlobalScope('university', function ($query) {
        if (!Auth::check() ||Auth::user()->hasRole('admin')) {
            return;
        }

        $query->where('university_id', Auth::user()->university_id);
    });
}


    public function university()
    {
        return $this->belongsTo(University::class);
    }
}
