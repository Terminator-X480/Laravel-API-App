<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\WpUser;
use App\Models\Vendor;
use App\Models\Lead;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'wp_mt_payments';

    // Set custom timestamp fields
    const CREATED_AT = 'created_on';
    const UPDATED_AT = 'updated_on';

    protected $fillable = [
        'lead_id',
        'user_id',
        'payment_method',
        'vendor_id',
        'b2b_vendor_id',
        'amount',
        'remaining',
        'company',
        'description',
        'payment_type',
    ];

    public function user()
    {
        return $this->belongsTo(WpUser::class, 'user_id', 'ID');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }
    public function b2b_vendor() {
    return $this->belongsTo(Vendor::class, 'b2b_vendor_id');
}
}