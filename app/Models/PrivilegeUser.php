<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PrivilegeUser extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'menu_id',
        'allowed',
        'c',
        'r',
        'u',
        'd',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'allowed' => 'boolean',
        'c' => 'boolean',
        'r' => 'boolean',
        'u' => 'boolean',
        'd' => 'boolean',
    ];

    /**
     * Get the user that owns the privilege.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the menu that owns the privilege.
     */
    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}
