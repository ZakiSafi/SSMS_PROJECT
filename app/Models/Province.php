<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Province extends Model
{
    use HasFactory;
    use HasFactory;
    protected $fillable = [
        'name',

    ];
    public function university(): HasMany
    {
        return $this->hasMany(University::class);
    }
}
