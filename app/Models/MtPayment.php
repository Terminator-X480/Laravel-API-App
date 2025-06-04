<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MtPayment extends Model
{
    protected $table = 'wp_mt_payments';

    protected $fillable = [
        'lead_id',
        'vendor_id',
        'amount',
        'user_id',
        'created_on',
    ];

    public $timestamps = false;

    public function lead()
    {
        return $this->belongsTo(WpMtLead::class, 'lead_id');
    }

    public function vendor()
    {
        return $this->belongsTo(MtVendor::class, 'vendor_id');
    }
}
