<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserBusinessUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'business_unit_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function businessUnit()
    {
        return $this->belongsTo(BusinessUnit::class);
    }
}
