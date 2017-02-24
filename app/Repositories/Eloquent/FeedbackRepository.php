<?php
/**
 * Created by PhpStorm.
 * User: fangweibo
 * Date: 2016/12/22
 * Time: 11:18
 */

namespace App\Repositories\Eloquent;


use App\Models\FeedBack;

class FeedbackRepository extends Repository
{
    public function model()
    {
        return FeedBack::class;
    }

    /**
     * 处理意见反馈
     * @param $request
     * @return bool
     * @author fangweibo
     */
    public function changeStatus($id,$status)
    {
        $data = $this->model->find($id);
        if($status==0){
            $data->status = 1;
        }else{
            $data->status = 0;
        }
        if($data->save()){
            return true;
        }else{
            return false;
        };
    }
}