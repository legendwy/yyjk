<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingAddress extends Model
{
    protected $table = 'shipping_address';
    protected $fillable = ['province','city','area','street', 'address', 'name','phone','user_id','status','postcode'];
}
