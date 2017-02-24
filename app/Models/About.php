<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class About extends Model
{
    protected $table = 'about_us';
    protected $fillable = ['content','address','tel','fax','email'];
}
