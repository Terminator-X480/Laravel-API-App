<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WpPost extends Model
{
    protected $table = 'wp_posts'; // Change if you have a prefix
    protected $primaryKey = 'ID';
    public $timestamps = false;
}
