<?php

namespace Webkul\Theme\Models;

use Illuminate\Database\Eloquent\Model;

class HeroSection extends Model
{
    protected $fillable = [
        'youtube_url',
        'youtube_height',
        'youtube_width',
        'top_image_url',
        'top_image_height',
        'top_image_width',
        'top_image_alt',
        'bottom_image_url',
        'bottom_image_height',
        'bottom_image_width',
        'bottom_image_alt',
        'top_image',
        'bottom_image',
    ];
}
