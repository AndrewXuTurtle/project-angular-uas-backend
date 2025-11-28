<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'password',
        'full_name',
        'level',
        'is_active',
    ];

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
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the business units that the user has access to.
     */
    public function businessUnits()
    {
        return $this->belongsToMany(BusinessUnit::class, 'user_business_units', 'user_id', 'business_unit_id')
            ->withTimestamps();
    }

    /**
     * Get the menus that the user has access to.
     */
    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'user_menus', 'user_id', 'menu_id')
            ->withTimestamps();
    }
}
