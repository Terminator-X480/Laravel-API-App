<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $table = 'wp_mt_leads'; // if you're using a custom table

    protected $guarded = []; // or define $fillable based on your fields
}
