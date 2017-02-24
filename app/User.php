<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Zizaco\Entrust\Traits\EntrustUserTrait;

class User extends Authenticatable
{
    use Notifiable;
//    use EntrustUserTrait;

    protected $table = 'users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'phone', 'email', 'password', 'status', 'nickname', 'headimgurl', 'sex', 'openid'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * 获取用户的收货地址
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * @author fangweibo
     */
    public function address()
    {
        return $this->hasMany('App\Models\ShippingAddress');
    }

    /**
     * 获取用户<=>收藏 关联信息
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * @author fangweibo
     */
    public function collect()
    {
        return $this->hasMany('App\Models\Collect');
    }

    /**
     * 用户<=>消费记录 关联关系
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * @author fangweibo
     */
    public function consume()
    {
        return $this->hasMany('App\Models\Consume');
    }

    /**
     * 用户<=>收益明细 关联关系
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * @author fangweibo
     */
    public function rebate()
    {
        return $this->hasMany('App\Models\Rebate');
    }
}
