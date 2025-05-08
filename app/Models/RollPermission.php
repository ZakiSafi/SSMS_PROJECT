<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RollPermission extends Model
{
    protected $fillable = [

        'name',
        'description',
    ];



    public function user()
    {
        return $this->belongsToMany(User::class);
    }

}
