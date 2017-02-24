<?php

namespace App\Repositories\Eloquent;
use App\Models\Refund;
use Carbon\Carbon;
use DB;
use App\Http\Controllers\Web\WechatController;

/**
 * 退款退货仓库
 * Class RefundRepository
 * @package App\Repositories\Eloquent
 */
class RefundRepository extends Repository {

    public function model()
    {
        return Refund::class;
    }

    /**
     * 获取列表
     * @param $request
     * @return mixed
     * @author: simayubo
     */
    public function getList($request){
        $input = $request->all();
        $where = [];
        if (!empty($input['order_goods_number'])) $where['order_goods.order_goods_number'] = $input['order_goods_number'];
        if (!empty($input['order_num'])) $where['order_info.order_num'] = $input['order_num'];
        if (!empty($input['phone'])) $where['users.phone'] = $input['phone'];
        if (!empty($input['goods_name'])) $where['order_goods.goods_name'] = $input['goods_name'];
        if (!empty($input['type']) && $input['type'] != 'all') $where['refund.type'] = $input['type'];
        if (!empty($input['status']) && $input['status'] != 'all') {
            if ($input['status'] == -1){
                $where['refund.status'] = 0;
            }else{
                $where['refund.status'] = $input['status'];
            }
        }

        $list = $this->model
            ->select('refund.*',
                'users.phone',
                'order_goods.order_goods_number', 'order_goods.goods_name', 'order_goods.goods_thumb', 'order_goods.goods_attr_values', 'order_goods.price', 'order_goods.num',
                'order_info.order_num',
                'reason.title'
                )
            ->where($where)
            ->leftJoin('users', 'refund.uid', '=', 'users.id')
            ->leftJoin('order_goods', 'refund.order_id', '=', 'order_goods.id')
            ->leftJoin('order_info', 'order_goods.order_id', '=', 'order_info.id')
            ->leftJoin('reason', 'refund.reason_id', '=', 'reason.id')
            ->orderBy('refund.id', 'desc')
            ->paginate(20);
        $list->appends($input)->render();

        return $list;
    }

    /**
     * 审核(用户发起退货退款申请)
     * @param $id
     * @return array
     * @author: simayubo
     */
    public function shenhe($id){
        $refund_info = DB::table('refund')->find($id);
        $order_goods_info = DB::table('order_goods')->find($refund_info->order_id);
        $order_info = DB::table('order_info')->find($order_goods_info->order_id);
        $order_goods_list = DB::table('order_goods')->where('order_id', $order_goods_info->order_id)->get();

        DB::beginTransaction();
        if ($refund_info->type == 1){
            $_money = round($order_goods_info->price * $order_goods_info->num, 2);
            //退款
            //修改状态
            $res1 = DB::table('refund')->where('id', $id)->update(['status' => 3]);
//            dump($res1);
            $res2 = DB::table('order_goods')->where('id', $refund_info->order_id)->update(['status' => 10]);
//            dump($res2);
            //更改用户余额
            $user_yue = DB::table('users')->where('id', $refund_info->uid)->increment('wallet', $_money);
//            dump($user_yue);
            //更改用户已用金额
            $user_use_yue = DB::table('users')->where('id', $refund_info->uid)->decrement('use_wallet', $_money);
//            dump($user_use_yue);
            //商品加库存
            $add_stock = DB::table('goods_attr')->where('id', $order_goods_info->goods_attr_id)->increment('stock', 1);
//            dump($user_use_yue);
            //获取用户余额
            $wallet = DB::table('users')->where('id', $refund_info->uid)->value('wallet');
            //添加用户金额日志
            $add_money_logs = DB::table('consumption_record')->insert([
                'user_id'   =>  $refund_info->uid,
                'money'     =>  $_money,
                'surplus_money' => $wallet,
                'use'       =>  '【编号：'.$order_goods_info->order_goods_number.'】退款获得',
                'status'    =>  1,
                'created_at'=>  Carbon::now(),
                'updated_at'=>  Carbon::now()
            ]);
//            dump($add_money_logs);

            if (!$res1 || !$res2 || !$user_yue || !$user_use_yue || !$add_money_logs || !$add_stock){
                DB::rollBack();
                return ['status' => 'fail', 'msg' => '系统异常'];
            }
            $is_edit_order_info = 1;
            foreach ($order_goods_list as $item) {
                if ($item->id != $refund_info->order_id){
                    if ($item->status != 10){
                        $is_edit_order_info = 0;
                    }
                }
            }
            //如果所有商品都退货，则退邮费，改大订单状态
            if ($is_edit_order_info == 1){
                $res3 = DB::table('order_info')->where('id', $order_goods_info->order_id)->update(['status' => 6]);
                if ($order_info->postage > 0){
                    //更改用户余额
                    $user_yue = DB::table('users')->where('id', $refund_info->uid)->increment('wallet', $order_info->postage);

                    //更改用户已用金额
                    $user_use_yue = DB::table('users')->where('id', $refund_info->uid)->decrement('use_wallet', $order_info->postage);
                    //获取用户余额
                    $_wallet = DB::table('users')->where('id', $refund_info->uid)->value('wallet');
                    //添加用户金额日志
                    $add_money_logs = DB::table('consumption_record')->insert([
                        'user_id'   =>  $refund_info->uid,
                        'money'     =>  $order_info->postage,
                        'surplus_money' => $_wallet,
                        'use'       =>  '【订单：'.$order_info->order_num.'】退款退邮费获得',
                        'status'    =>  1,
                        'created_at'=>  Carbon::now(),
                        'updated_at'=>  Carbon::now()
                    ]);
                }else{
                    $user_yue = $user_use_yue = $add_money_logs = 1;
                }

                if (!$res3 || !$user_yue || !$user_use_yue || !$add_money_logs){
                    DB::rollBack();
                    return ['status' => 'fail', 'msg' => '系统异常'];
                }
            }
        }elseif($refund_info->type == 2){
            //退货
            $res1 = DB::table('refund')->where('id', $id)->update(['status' => 2]);
            $res2 = DB::table('order_goods')->where('id', $refund_info->order_id)->update(['status' => 7]);

            if (!$res1 || !$res2){
                DB::rollBack();
                return ['status' => 'fail', 'msg' => '系统异常'];
            }
        }else{
            DB::rollBack();
            return ['status' => 'fail', 'msg' => '系统异常'];
        }
        DB::commit();
        $wechat = new WechatController();
        $wechat->sendTemplateMessage($id);
        return ['status' => 'success', 'msg' => '操作成功'];
    }

    /**
     * 拒绝退款退货（用户发起退款退货）
     * @param $id
     * @return array
     * @author: simayubo
     */
    public function jujue($id){
        $refund_info = DB::table('refund')->find($id);
        $order_goods_info = DB::table('order_goods')->find($refund_info->order_id);
        $order_info = DB::table('order_info')->find($order_goods_info->order_id);

        DB::beginTransaction();
        if ($refund_info->type == 1){
            //退款
            $res1 = DB::table('refund')->where('id', $id)->update(['status' => 1]);
            $res2 = DB::table('order_goods')->where('id', $refund_info->order_id)->update(['status' => 1]);

            if (!$res1 || !$res2){
                DB::rollBack();
                return ['status' => 'fail', 'msg' => '系统异常'];
            }
        }elseif($refund_info->type == 2){
            //退货
            $res1 = DB::table('refund')->where('id', $id)->update(['status' => 1]);
            $res2 = DB::table('order_goods')->where('id', $refund_info->order_id)->update(['status' => $order_info->status]);

            if (!$res1 || !$res2){
                DB::rollBack();
                return ['status' => 'fail', 'msg' => '系统异常'];
            }
        }else{
            DB::rollBack();
            return ['status' => 'fail', 'msg' => '系统异常'];
        }

        DB::commit();
        $wechat = new WechatController();
        $wechat->sendTemplateMessage($id);
        return ['status' => 'success', 'msg' => '操作成功'];
    }

    /**
     * 后台确认收货
     * @param $id
     * @return array
     * @author: simayubo
     */
    public function shouhuo($id){
        $refund_info = DB::table('refund')->find($id);
        $order_goods_info = DB::table('order_goods')->find($refund_info->order_id);
        $order_info = DB::table('order_info')->find($order_goods_info->order_id);
        $order_goods_list = DB::table('order_goods')->where('order_id', $order_goods_info->order_id)->get();

        $res1 = DB::table('refund')->where('id', $id)->update(['status' => 4]);
        $res2 = DB::table('order_goods')->where('id', $refund_info->order_id)->update(['status' => 8]);

        $_money = round($order_goods_info->price * $order_goods_info->num, 2);
        //更改用户余额
        $user_yue = DB::table('users')->where('id', $refund_info->uid)->increment('wallet', $_money);
        //更改用户已用金额
        $user_use_yue = DB::table('users')->where('id', $refund_info->uid)->decrement('use_wallet', $_money);
        //获取用户余额
        $wallet = DB::table('users')->where('id', $refund_info->uid)->value('wallet');
        //商品加库存
        $add_stock = DB::table('goods_attr')->where('id', $order_goods_info->goods_attr_id)->increment('stock', 1);
        //添加用户金额日志
        $add_money_logs = DB::table('consumption_record')->insert([
            'user_id'   =>  $refund_info->uid,
            'money'     =>  $_money,
            'surplus_money' => $wallet,
            'use'       =>  '【编号：'.$order_goods_info->order_goods_number.'】退货获得',
            'status'    =>  1,
            'created_at'=>  Carbon::now(),
            'updated_at'=>  Carbon::now()
        ]);

        if (!$res1 || !$res2 || !$user_yue || !$user_use_yue || !$add_money_logs || !$add_stock){
            DB::rollBack();
            return ['status' => 'fail', 'msg' => '系统异常'];
        }

        $is_edit_order_info = 1;
        foreach ($order_goods_list as $item) {
            if ($item->id != $refund_info->order_id){
                if ($item->status != 10 && $item->status != 8){
                    $is_edit_order_info = 0;
                }
            }
        }
        //如果都退货了
        if ($is_edit_order_info == 1){
            $res3 = DB::table('order_info')->where('id', $order_goods_info->order_id)->update(['status' => 6]);
            if (!$res3){
                DB::rollBack();
                return ['status' => 'fail', 'msg' => '系统异常'];
            }
        }

        DB::commit();
        $wechat = new WechatController();
        $wechat->sendTemplateMessage($id);
        return ['status' => 'success', 'msg' => '操作成功'];
    }
    /**
     * 后台拒绝收货
     * @param $id
     * @return array
     * @author: simayubo
     */
    public function jujueShouhuo($id){
        $refund_info = DB::table('refund')->find($id);
        $order_goods_info = DB::table('order_goods')->find($refund_info->order_id);
        $order_info = DB::table('order_info')->find($order_goods_info->order_id);

        DB::beginTransaction();

        $res1 = DB::table('refund')->where('id', $id)->update(['status' => 1]);
        $res2 = DB::table('order_goods')->where('id', $refund_info->order_id)->update(['status' => $order_info->status]);

        if (!$res1 || !$res2){
            DB::rollBack();
            return ['status' => 'fail', 'msg' => '系统异常'];
        }

        DB::commit();
        $wechat = new WechatController();
        $wechat->sendTemplateMessage($id, 99);
        return ['status' => 'success', 'msg' => '操作成功'];
    }

    /**
     * 通过退单id查询物流信息
     * @param $id
     * @return bool|mixed
     * @author: simayubo
     */
    public function getWuliuTracesByRefundId($id){
        $refund_info = $this->model->find($id)->toArray();
        if ($refund_info['is_set'] == 1){
            $wuliu_gongsi = \DB::table('logistics')->where('id', $refund_info['company'])->first();
            if ($wuliu_gongsi){
                $res = get_wuliu_traces($wuliu_gongsi->bm, $refund_info['number']);
                return $res;
            }else{
                return false;
            }
        }

        return false;
    }


}