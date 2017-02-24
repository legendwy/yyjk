<?php

namespace App\Http\Controllers\Comment;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use PhpParser\Node\Scalar\DNumber;

class DistributionController extends Controller
{

    /**
     * 自动取消未支付订单
     * @Author wangyan
     */
    public function checkAwayOrder(){
        $day = DB::table('config')->where(['id' => 8])->first();
        $time = 24 * 3600 * $day->value;
        $list = DB::table('order_info')->where(['status' => -1])->get();
        $array = [];
        foreach ($list as $key => $item){
            $add_time = strtotime( $item->add_time);
            if($add_time<=time() - $time){
                $array[] = $list[$key];
            }
        }
        if(empty($array)){
            echo 'no list';
            return;
        }
        DB::beginTransaction();
        $status = 1;
        foreach ($array as $k => $v){
            $order_goods = DB::table('order_goods')->where(['order_id' => $v->id])->get();
            foreach ($order_goods as $_key => $_val){
                //改变商品订单的状态
                $res1 =  DB::table('order_goods')->where(['id' => $_val->id])->update(['status' => 5]);
                if($res1>0){
                    $goods_attr = DB::table('goods_attr')->where(['id' => $_val->goods_attr_id])->count();
                    //加库存
                    if($goods_attr>0){
                        $add_stock = DB::table('goods_attr')->where(['id' => $_val->goods_attr_id])->increment('stock', $_val->num);
                        if(!$add_stock)$status = 0;
                    }
                }else{
                    $status = 0;
                }
            }
            //改变订单状态
            $res2 = DB::table('order_info')->where(['id' => $v->id])->update(['status' => 5]);
            if(!$res2)$status = 0;
        }
        if($status = 1){
            DB::commit();
            echo 'Success!';
        }else{
            DB::rollBack();
            echo 'Error!';
        }
    }

    /**
     * 自动确认收货
     * @Author wangyan
     */
    public function checkConfirmOrder(){
        $day = DB::table('config')->where(['id' => 9])->first();
        $time = 24 * 3600 * $day->value;
        $list = DB::table('order_info')->where(['status' => 2])->get();
        $array = [];
        foreach ($list as $key => $item){
            $fahuo_time = strtotime( $item->fahuo_time);
            if($fahuo_time<=time() - $time){
                $array[] = $list[$key];
            }
        }
        if(empty($array)){
            echo 'no list';
            return;
        }
        DB::beginTransaction();
        $status = 1;
        foreach ($array as $k => $v){
            $order_goods = DB::table('order_goods')->where(['order_id' => $v->id])->get();
            foreach ($order_goods as $_key => $_val){
                $_val = get_object_vars($_val);
                if($_val['status'] == 2){
                    //改变订单商品的状态
                    $res1 = DB::table('order_goods')->where(['id'=>$_val['id']])
                        ->update([
                            'status'       =>     3,
                            'shou_time'    =>     \Carbon\Carbon::now(),
                        ]);
                    if(!$res1)$status = 0;
                }
            }
            //改变订单状态
            $res2 = DB::table('order_info')->where(['id' => $v->id])
                ->update([
                    'status'      =>      3,
                    'shou_time'   =>      \Carbon\Carbon::now(),
                ]);
            if(!$res2)$status = 0;

            //返利
            $res3 = $this->rebate($v->id, $v->user_id);
            if(!$res3)$status = 0;
        }
        if($status = 1){
            DB::commit();
            echo 'Success!';
        }else{
            DB::rollBack();
            echo 'Error!';
        }
    }

    /**
     * 下架限时商品
     * @Author wangyan
     */
    public function checkXianGoods(){
        $goods_list = DB::table('goods')->where(['xian' => 1])->get();
        if(empty($goods_list)){
            echo 'no list!';
            return;
        }
        foreach ($goods_list as $item){
            if(strtotime($item->date_end) <= time()){
                $res = DB::table('goods')->where(['id' => $item->id])->save(['xian' => -1, 'status' => -2]);
                if($res>0){
                   echo 'Success!';
                }else {
                    echo 'Error!';
                }
            }
        }
    }

    /**
     * 订单返利
     * @param $order_id
     * @Author wangyan
     */
    public function rebate($order_id, $user_id){
        $order_info = \DB::table('order_info')->where(['id' => $order_id])->first();
        $order_info = get_object_vars($order_info);
//        dd($order_info);
        $user_info = \DB::table('users')->where(['id' => $user_id])->first();
        $user_info = get_object_vars($user_info);
        $one_credit = 0;
        $two_credit = 0;
        $qu_credit = 0;
        $vip_credit = 0;
        $order_goods = \DB::table('order_goods')->where(['order_id' => $order_info['id']])->get()->toArray();
//        dd($order_goods);
        foreach ($order_goods as $item){
            $item = get_object_vars($item);
            if($item['status'] == 3){
                //一级返利
                $one_credit += $item['one_bili'] * $item['price'] * $item['num'];
                //二级返利
                $two_credit += $item['two_bili'] * $item['price'] * $item['num'];
                //区域代理返利
                $qu_credit += $item['qu_bili'] * $item['price'] * $item['num'];
                //VIP代理返利
                $vip_credit += $item['vip_bili'] * $item['price'] * $item['num'];
            }
        }
        $status = 1; //初始化状态
        $res1 = $res2 = $res3 = $res4 = 1;
        $sql1 = $sql2 = $sql3 = $sql4 = $sql5 = $sql6 = $sql7 = $sql8 = 1;
        //有上级->返利
        if(!empty($user_info['pid'])){
            //余额
            $one_user_info = DB::table('users')->where(['id' => $user_info['pid']])->first();
            $res1 = DB::table('users')->where(['id' => $one_user_info->id])
                ->update([
                    'wallet'            =>    $one_user_info->wallet + $one_credit,
                    'fenxiao_credit'    =>    $one_user_info->fenxiao_credit + $one_credit
                ]);

            $wallet = DB::table('users')->where(['id' => $user_info['pid']])->value('wallet');
            //记录
            $sql1 = DB::table('consumption_record')->insert([
                'user_id'     =>     $one_user_info->id,
                'money'       =>     $one_credit,
                'surplus_money'=>    $wallet,
                'use'         =>     '支付订单【'.$order_info['order_num'].'】分销返利',
                'status'      =>     1,
                'created_at'  =>     \Carbon\Carbon::now(),
                'updated_at'  =>     \Carbon\Carbon::now(),
            ]);

            $sql2 = DB::table('rebate_record')->insert([
                'user_id'       =>     $one_user_info->id,
                'source_id'     =>     $order_info['user_id'],
                'order_id'      =>     $order_id,
                'credit'        =>     $one_credit,
                'remark'        =>     '支付订单【'.$order_info['order_num'].'】一级返利',
                'garde'         =>     1,
                'created_at'    =>     \Carbon\Carbon::now(),
                'updated_at'    =>     \Carbon\Carbon::now(),
            ]);

            $sql3 = DB::table('user_gains_logs')->insert([
                'user_id'       =>     $one_user_info->id,
                'source_id'     =>     $order_info['user_id'],
                'money'         =>     $one_credit,
                'remark'        =>     getUserInfoById($order_info['user_id'])->nickname.'的支付订单【'.$order_info['order_num'].'】一级返利',
                'created_at'    =>     \Carbon\Carbon::now(),
                'updated_at'    =>     \Carbon\Carbon::now(),
            ]);

            if(!$res1 && !$sql1 && !$sql2 &!$sql3) $status = 0;

            //有上上级->返利
            if(!empty($one_user_info->pid)){
                $two_user_info = DB::table('users')->where(['id' => $one_user_info->pid])->first();
                $res2 = DB::table('users')->where(['id' => $two_user_info->id])
                    ->update([
                        'wallet'            =>    $two_user_info->wallet + $two_credit,
                        'fenxiao_credit'    =>    $two_user_info->fenxiao_credit + $two_credit
                    ]);
                $wallet = DB::table('users')->where(['id' => $one_user_info->pid])->value('wallet');
                $sql4 = DB::table('consumption_record')->insert([
                    'user_id'     =>      $two_user_info->id,
                    'money'       =>      $two_credit,
                    'surplus_money'=>     $wallet,
                    'use'         =>      '支付订单【'.$order_info['order_num'].'】分销返利',
                    'status'      =>      1,
                    'created_at'  =>      \Carbon\Carbon::now(),
                    'updated_at'  =>      \Carbon\Carbon::now(),
                ]);

                $sql5 = DB::table('rebate_record')->insert([
                    'user_id'       =>     $two_user_info->id,
                    'source_id'     =>     $order_info['user_id'],
                    'order_id'      =>     $order_id,
                    'credit'        =>     $two_credit,
                    'remark'        =>     '支付订单【'.$order_info['order_num'].'】二级返利',
                    'garde'         =>     2,
                    'created_at'    =>     \Carbon\Carbon::now(),
                    'updated_at'    =>     \Carbon\Carbon::now(),
                ]);

                $sql6 = DB::table('user_gains_logs')->insert([
                    'user_id'       =>     $two_user_info->id,
                    'source_id'     =>     $order_info['user_id'],
                    'money'         =>     $two_credit,
                    'remark'        =>     getUserInfoById($order_info['user_id'])->nickname.'的支付订单【'.$order_info['order_num'].'】二级返利',
                    'created_at'    =>     \Carbon\Carbon::now(),
                    'updated_at'    =>     \Carbon\Carbon::now(),
                ]);

                if(!$res2 && !$sql4 && !$sql5 && !$sql6) $status = 0;
            }
        }
        //区域代理/VIP代理
		
        $daili = [];
        $area_daili = DB::table('agency')->where(['province' => $order_info['province'], 'city'=>$order_info['city'], 'area'=>$order_info['area'], 'level'=> 3])->first();
        if(empty($area_daili)){
            $city_daili = DB::table('agency')->where(['province' => $order_info['province'], 'city'=>$order_info['city'], 'level' => 2])->first();
            if(empty($city_daili)){
                    $province_daili = DB::table('agency')->where(['province' => $order_info['province'], 'level'=> 1])->first();
                    if(!empty($province_daili)){
                        $daili = $province_daili;
                    }
            }else{
                $daili = $city_daili;
            }
        }else{
            $daili = $area_daili;
        }

        if(!empty($daili)){
            $_res = DB::table('users')->where(['id' =>$daili->user_id])->first();
            if($daili->daili == 2){
                //区域代理
                $res3 = DB::table('users')->where(['id' => $daili->user_id])
                    ->update([
                        'wallet'          =>    $_res->wallet + $qu_credit,
                        'agent_credit'    =>    $_res->agent_credit + $qu_credit
                    ]);
                $wallet = DB::table('users')->where(['id' =>$daili->user_id])->value('wallet');

                $sql7 = DB::table('consumption_record')->insert([
                    'user_id'     =>      $daili->user_id,
                    'money'       =>      $qu_credit,
                    'surplus_money'=>     $wallet,
                    'use'         =>      '支付订单【'.$order_info['order_num'].'】区域代理返利',
                    'status'      =>      1,
                    'created_at'  =>      \Carbon\Carbon::now(),
                    'updated_at'  =>      \Carbon\Carbon::now(),
                ]);

                $sql8 = DB::table('user_gains_logs')->insert([
                    'user_id'       =>     $daili->user_id,
                    'source_id'     =>     $order_info['user_id'],
                    'money'         =>     $qu_credit,
                    'remark'        =>     getUserInfoById($order_info['user_id'])->nickname.'的支付订单【'.$order_info['order_num'].'】区域代理返利',
                    'created_at'  =>      \Carbon\Carbon::now(),
                    'updated_at'  =>      \Carbon\Carbon::now(),
                ]);

            }elseif ($daili->daili == 3){
                //vip代理
                $res3 = DB::table('users')->where(['id' => $daili->user_id])
                    ->update([
                        'wallet'          =>    $_res->wallet + $vip_credit,
                        'agent_credit'    =>    $_res->agent_credit + $vip_credit
                    ]);
                $wallet = DB::table('users')->where(['id' =>$daili->user_id])->value('wallet');

                $sql7 = DB::table('consumption_record')->insert([
                    'user_id'     =>      $daili->user_id,
                    'money'       =>      $vip_credit,
                    'surplus_money'=>     $wallet,
                    'use'         =>      '支付订单【'.$order_info['order_num'].'】Vip代理返利',
                    'status'      =>      1,
                    'created_at'  =>      \Carbon\Carbon::now(),
                    'updated_at'  =>      \Carbon\Carbon::now(),
                ]);

                $sql8 = DB::table('user_gains_logs')->insert([
                    'user_id'       =>     $daili->user_id,
                    'source_id'     =>     $order_info['user_id'],
                    'money'         =>     $vip_credit,
                    'remark'        =>     getUserInfoById($order_info['user_id'])->nickname.'的支付订单【'.$order_info['order_num'].'】Vip代理返利',
                    'created_at'    =>     \Carbon\Carbon::now(),
                    'updated_at'    =>     \Carbon\Carbon::now(),
                ]);
            }
            if(!$res3 && !$sql7 && !$sql8) $status = 0;
        }

        if ($status == 1){
            return true;
        }else{
            return false;
        }
    }




}
