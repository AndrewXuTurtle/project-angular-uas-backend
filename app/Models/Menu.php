<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Menu extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nama_menu',
        'url_link',
        'parent',
    ];

    /**
     * Get the parent menu.
     */
    public function parentMenu()
    {
        return $this->belongsTo(Menu::class, 'parent');
    }

    /**
     * Get the children menus.
     */
    public function children()
    {
        return $this->hasMany(Menu::class, 'parent');
    }

    /**
     * Get all children menus recursively.
     */
    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }

    /**
     * Get the privilege users for the menu.
     */
    public function privilegeUsers()
    {
        return $this->hasMany(PrivilegeUser::class);
    }
}
