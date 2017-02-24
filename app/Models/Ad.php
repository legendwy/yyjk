<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    protected $table = 'ad';

    protected $fillable = [
        'url',
        'image',
        'position_id',
        'sort'
    ];
}
