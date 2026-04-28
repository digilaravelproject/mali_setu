<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $table = 'pages'; // table name

    protected $fillable = [
        'page_name',
        'page_type',
        'description',
        'status'
    ];
}