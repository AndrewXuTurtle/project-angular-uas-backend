<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BusinessUnit extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'business_unit',
        'active',
    ];

    /**
     * Get the users that have access to this business unit.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_business_units', 'business_unit_id', 'user_id')
            ->withTimestamps();
    }
}
