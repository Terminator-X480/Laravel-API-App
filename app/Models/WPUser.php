<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WPUser extends Model
{
    protected $table = 'wp_users';   // Your WP users table name
    protected $primaryKey = 'ID';    // WP users primary key is 'ID'
    public $timestamps = false;      // WP tables do NOT have Laravel timestamps
}
