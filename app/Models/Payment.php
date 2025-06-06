<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'wp_mt_payments';

    // Tell Laravel to use custom timestamp columns
    const CREATED_AT = 'created_on';
    const UPDATED_AT = 'updated_on';

    protected $fillable = [
        'lead_id',
        'user_id',
        'payment_method',
        'vendor_id',
        'b2b_vendor_id',
        'amount',
        'company',
        'description',
        'payment_type',
    ];
}
