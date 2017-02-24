<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Logistics extends Model
{
    protected $table = 'logistics';
    protected $fillable = [
        'name', 'bm', 'phone'
    ];
}
