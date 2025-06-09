<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $table = 'wp_mt_vendors';
    protected $primaryKey = 'id';

    public $timestamps = true;

    const CREATED_AT = 'created_at';

    protected $dates = ['created_at'];

    protected $fillable = [
        'name',
        'phone',
        'location',
        'type',
    ];
}
