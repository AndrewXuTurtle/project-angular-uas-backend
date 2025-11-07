<?php

namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'token',
        'abilities',
        'business_unit_id',
        'expires_at',
        'last_used_at',
    ];

    /**
     * Get the business unit associated with the token.
     */
    public function businessUnit()
    {
        return $this->belongsTo(BusinessUnit::class);
    }
}
