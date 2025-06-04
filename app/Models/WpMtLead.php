<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WpMtLead extends Model
{
    protected $table = 'wp_mt_leads';
    protected $primaryKey = 'id';

    public $timestamps = true;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'modified_at'; // 👈 Tell Laravel to use this column

    protected $dates = ['created_at', 'modified_at'];

    protected $fillable = [
        'name', 'country_code', 'phone', 'email', 'no_of_people', 'trek_date',
        'message', 'source', 'device', 'type', 'type_id', 'is_converted',
        'is_follow_up', 'bot', 'lead', 'recording_file', 'phone_id',
        'trek_name', // 👈 Make sure not to include created_at here
    ];
}
