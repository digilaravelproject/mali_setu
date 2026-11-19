<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageHero extends Model
{
    protected $fillable = [
        'title',
        'functionality_name',
        'event_name',
        'image_path',
        'web_image_path',
        'url',
        'external_url',
    ];
}
