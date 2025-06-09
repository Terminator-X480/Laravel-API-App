<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $table = 'wp_mt_leads'; // if you're using a custom table

    // Tell Laravel to use custom timestamp columns
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'modified_at';
    
    protected $fillable = ['remaining'];
}
