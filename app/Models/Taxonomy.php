<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WpTerm extends Model
{
    protected $table = 'wp_terms';
    protected $primaryKey = 'term_id';
    public $timestamps = false;

    protected $fillable = ['name'];
}
