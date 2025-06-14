<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'university_id',
        'user_agent',
        'action_type',
        'action_description',
        'table_name',
        'record_id',
        'ip_address',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function studentStatistic()
    {
        return $this->belongsTo(StudentStatistic::class, 'record_id');
    }
}
