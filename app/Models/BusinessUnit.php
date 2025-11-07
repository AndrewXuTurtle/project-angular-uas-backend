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
        'user_id',
        'active',
    ];

    /**
     * Get the user that owns the business unit.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
