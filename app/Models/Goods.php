<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Goods extends Model
{
    //
    public function attr()
    {
        return $this->hasMany('App\Models\GoodsAttr');
    }
}
