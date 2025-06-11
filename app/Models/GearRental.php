<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GearRental extends Model
{
    protected $table = 'wp_mt_gear_rentals'; // Your existing WordPress table

    // Tell Laravel to use custom timestamp columns
    const CREATED_AT = 'created_on';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'vendor_id',
        'location',
        'equipment',
        'purchase_date',
        'payment_type',
        'payment',
    ];

    public $timestamps = true; // uses created_at and updated_at
}
