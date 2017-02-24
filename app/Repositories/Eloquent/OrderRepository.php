<?php



namespace App\Repositories\Eloquent;





use App\Models\OrderInfo;



class OrderRepository extends Repository

{

    public function model()

    {

        return OrderInfo::class;

    }



   /**
     * 获取订单列表
     * @param $request
     * @param string $is_page
     * @return mixed
     * @author: simayubo
     */
    public function getOrderInfoList($request, $is_page = 'yes'){

        $input = $request->all();
        $where[] = ['order_info.status', '>=', -1];
        if (!empty($input['order_num'])) $where[] = ['order_info.order_num', '=', $input['order_num']];
        if (!empty($input['nickname'])) $where[] = ['users.nickname', '=', $input['nickname']];
        if (!empty($input['name'])) $where[] = ['users.phone', '=', $input['name']];
        if (!empty($input['status']) && $input['status'] != 0) $where[] = ['order_info.status', '=', $input['status']];
        if (isset($input['pay_status']) && $input['pay_status'] != 'all') {
            if ($input['pay_status'] == -1){
                $where[] = ['order_info.pay_status', '=', 0];
            }else{
                $where[] = ['order_info.pay_status', '=', $input['pay_status']];
            }
        }

        if (!empty($input['date_star']) && empty($input['date_end'])){
            $where[] = ['order_info.add_time', '>=', $input['date_star']];
        }elseif (empty($input['date_star']) && !empty($input['date_end'])){
            $where[] = ['order_info.add_time', '<=', $input['date_end']];
        }elseif(!empty($input['date_star']) && !empty($input['date_end'])){
            $where[] = ['order_info.add_time', '>=', $input['date_star']];
            $where[] = ['order_info.add_time', '<', $input['date_end']];
        }
        //加分页
        if ($is_page == 'yes'){
            $list = $this->model
                ->select('order_info.*', 'users.phone', 'users.nickname', 'a.REGION_NAME as province', 'b.REGION_NAME as city', 'c.REGION_NAME as area')
                ->where($where)
                ->leftJoin('users', 'order_info.user_id', '=', 'users.id')
                ->leftJoin('region as a', 'order_info.province', '=', 'a.REGION_ID')
                ->leftJoin('region as b', 'order_info.city', '=', 'b.REGION_ID')
                ->leftJoin('region as c', 'order_info.area', '=', 'c.REGION_ID')
                ->orderBy('order_info.id', 'desc')
                ->paginate(20);
            $list->appends($input)->render();
        }else{
            //不加分页
            $list = $this->model
                ->select('order_info.*', 'users.phone', 'a.REGION_NAME as province', 'b.REGION_NAME as city', 'c.REGION_NAME as area')
                ->where($where)
                ->leftJoin('users', 'order_info.user_id', '=', 'users.id')
                ->leftJoin('region as a', 'order_info.province', '=', 'a.REGION_ID')
                ->leftJoin('region as b', 'order_info.city', '=', 'b.REGION_ID')
                ->leftJoin('region as c', 'order_info.area', '=', 'c.REGION_ID')
                ->orderBy('order_info.id', 'desc')
                ->get();
        }

        return $list;
    }


    /**

     * 获取订单详情

     * @param $id

     * @return mixed

     * @author: simayubo

     */

    public function getOrderInfo($id){

        $info = $this->model

            ->select('order_info.*', 'a.REGION_NAME as province', 'b.REGION_NAME as city', 'c.REGION_NAME as area')

            ->where([['order_info.status', '>', -2], ['order_info.id', '=', $id]])

            ->leftJoin('users', 'order_info.user_id', '=', 'users.id')

            ->leftJoin('region as a', 'order_info.province', '=', 'a.REGION_ID')

            ->leftJoin('region as b', 'order_info.city', '=', 'b.REGION_ID')

            ->leftJoin('region as c', 'order_info.area', '=', 'c.REGION_ID')

            ->first();



        return $info;

    }



    /**

     * 取消订单（兼容前台调用，前台调用必须传订单id以及操作用户id）

     * @param $id

     * @param int $user_id

     * @return array

     * @author: simayubo

     */

    public function cancelOrder($id, $user_id = 0){



        $order_info = $this->model->find($id);

        if (!$order_info){

            return ['status' => -1, 'msg' => '订单不存在'];

        }

        if ($order_info->status != -1){

            return ['status' => -2, 'msg' => '当前订单状态不支持取消'];

        }

        if ($user_id != 0){

            if ($order_info['user_id'] != $user_id) return ['status' => -4, 'msg' => '当前订单用户和操作用户不一致'];

        }

        \DB::beginTransaction();

        //修改大订单状态

        $order_info = \DB::table('order_info')->where(['id' => $id])->update(['status' => 5]);

        $order_goods = \DB::table('order_goods')->where(['order_id' => $id])->update(['status' => 5]);

        if ($order_info && $order_goods){

            \DB::commit();

            return ['status' => 1, 'msg' => '取消订单成功'];

        }else{

            \DB::rollBack();

            return ['status' => -3, 'msg' => '取消订单失败'];

        }



    }



    /**

     * 删除订单（兼容前台调用，前台调用必须传订单id以及操作用户id）

     * @param $id

     * @param int $user_id

     * @return array

     * @author: simayubo

     */

    public function deleteOrder($id, $user_id = 0){

        $order_info = $this->model->find($id);

        if (!$order_info){

            return ['status' => -1, 'msg' => '订单不存在'];

        }

        if ($order_info->status != 4 && $order_info->status != 5 && $order_info->status != 6){

            return ['status' => -2, 'msg' => '当前订单状态不能删除'];

        }

        if ($user_id != 0){

            if ($order_info['user_id'] != $user_id) return ['status' => -4, 'msg' => '当前订单用户和操作用户不一致'];

        }

        \DB::beginTransaction();

        //修改大订单状态

        $order_info = \DB::table('order_info')->where(['id' => $id])->update(['status' => -2]);

        $order_goods = \DB::table('order_goods')->where(['order_id' => $id])->update(['status' => -2]);

        if ($order_info && $order_goods){

            \DB::commit();

            return ['status' => 1, 'msg' => '删除订单成功'];

        }else{

            \DB::rollBack();

            return ['status' => -3, 'msg' => '删除订单失败'];

        }

    }



    /**

     * 通过订单id查询物流信息

     * 物流状态: 0-无轨迹，1-已揽收，2-在途中 201-到达派件城市，3-签收,4-问题件

     * @param $id

     * @return bool|mixed

     * @author: simayubo

     */

    public function getWuliuTracesByOrderId($id){

        $order_info = $this->model->find($id);

//        if ($order_info['status'] == 2 || $order_info['status'] == 3 || $order_info['status'] == 4 || $order_info['status'] == 6){

        if (!empty($order_info['wuliu_gongsi'])){

            $wuliu_gongsi = \DB::table('logistics')->where('id', $order_info['wuliu_gongsi'])->first();

            $res = get_wuliu_traces($wuliu_gongsi->bm, $order_info['wuliu_num']);

            $res['name'] = $wuliu_gongsi->name;
            $res['phone'] = $wuliu_gongsi->phone;

            return $res;

        }



        return false;

    }



    /**

     * 通过状态获取统计

     * @param $status

     * @return mixed

     * @author: simayubo

     */

    public function getCountByStatus($status = -3, $user_id){

        $where = [

            'user_id' => $user_id

        ];

        if ($status != -3){

            $where['status'] = $status;

        }

        if ($status == -99){

            return \DB::table('refund')->where('uid', $user_id)->whereIn('status', [0, 2])->count('id');

        }else{

            return $this->model->where($where)->count('id');

        }

    }

}