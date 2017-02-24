<?php
/**
 * Created by PhpStorm.
 * User: fangweibo
 * Date: 2016/12/19
 * Time: 12:30
 */

namespace App\Repositories\Eloquent;


use App\Models\Collect;

class CollectRepository extends Repository
{
    public function model()
    {
        return Collect::class;
    }

    /**
     * 获取用户收藏列表
     * @param $user_id
     * @return mixed
     * @author fangweibo
     */
    public function getUserCollect($user_id)
    {
        $list = $this->model->where('user_id',$user_id)->get();

        return $list;
    }
}