<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $table = 'ad_image';

    protected $fillable = [
        'image',
        'size',
    ];
}
