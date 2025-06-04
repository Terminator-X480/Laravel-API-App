<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MtBooking extends Model
{
    protected $table = 'wp_mt_bookings';

    protected $fillable = [
        'lead_id',
        'vendor_id',
        'is_book',
        'is_cancel',
        'created_at',
    ];

    public $timestamps = false; // Because this table likely doesn't use Laravel's created_at/updated_at

    // Relationships (optional)
    public function lead()
    {
        return $this->belongsTo(WpMtLead::class, 'lead_id');
    }

    public function vendor()
    {
        return $this->belongsTo(MtVendor::class, 'vendor_id');
    }
}
