<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'business_unit_id',
    ];

    /**
     * Get the business unit that owns the customer.
     */
    public function businessUnit()
    {
        return $this->belongsTo(BusinessUnit::class);
    }
}
