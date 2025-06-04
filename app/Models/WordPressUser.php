<?php

// app/Models/WordPressUser.php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class WordPressUser extends Authenticatable
{
    protected $connection = 'wordpress';
    protected $table = 'wp_users';
    protected $primaryKey = 'ID';
    protected $fillable = ['user_login', 'user_pass', 'user_email'];
    public $timestamps = false;

    public function getAuthPassword()
    {
        return $this->user_pass;
    }
}
