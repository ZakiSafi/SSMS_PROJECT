<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

/**
 * @method bool hasRole(string $role)
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $guard_name = 'api'; // Add this

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'university_id',
        'email',
        "university_id",
        'password',
        'role_id',
        'image'
    ];

    public function university()
    {
        return $this->belongsTo(University::class);
    }

    public function logs()
    {
        return $this->hasMany(Log::class);
    }

    public function studentStatistics()
    {
        return $this->hasMany(StudentStatistic::class);
    }

    // The role() relationship is not needed with Spatie Laravel Permission.
    // Use the roles and permissions relationships provided by the package.

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
