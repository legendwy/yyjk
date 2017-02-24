<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\Eloquent\OrderRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\WechatController;

class OrderController extends Controller
{
    protected $order;

    public function __construct(OrderRepository $order)
    {
        $this->middleware('check.permission:order');
        $this->order = $order;
    }

    /**
     * 订单列表
     * @param Request $request
     * @return $this
     * @author: simayubo
     */
    public function index(Request $request)
    {

        $list = $this->order->getOrderInfoList($request);
        return view('admin.order.list')->with(compact('list'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $goods_order_list = \DB::table('order_goods')->where(['order_id' => $id, 'del' => 0])->get();
        $order_info = $this->order->getOrderInfo($id);

        $wuliu = $this->order->getWuliuTracesByOrderId($id);
//dd($wuliu);
        return view('admin.order.show')->with(compact('goods_order_list', 'order_info', 'wuliu'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $resault = $this->order->deleteOrder($id);
        return $resault;
    }

    /**
     * 取消订单
     * @param $id
     * @return array
     * @author: simayubo
     */
    public function cancelOrder($id){
        if (!request()->user('admin')->can('order.edit')) {
           return ['status' => 'fail', 'msg' => '没有权限'];
        }
        return $this->order->cancelOrder($id);
    }

    /**
     * 发货
     * @param Request $request
     * @param $id
     * @return $this|array
     * @author: simayubo
     */
    public function fahuo(Request $request, $id){
        if (!request()->user('admin')->can('order.edit')) {
            return ['status' => 'fail', 'msg' => '没有权限'];
        }
        if ($request->isMethod('post')){
            if (empty($request->input('wuliu_num'))){
                return ['status' => 'fail', 'msg' => '物流单号不能为空'];
            }
            return $this->doFahuo($request, $id);
        }
        $wuliu = \DB::table('logistics')->get();

        return view('admin.order.fahuo')->with(compact('wuliu', 'id'));
    }
    /**
     * 编辑物流
     * @param Request $request
     * @param $id
     * @return $this|array
     * @author: simayubo
     */
    public function editWuliu(Request $request, $id){
        if (!request()->user('admin')->can('order.edit')) {
            return ['status' => 'fail', 'msg' => '没有权限'];
        }
        if ($request->isMethod('post')){
            if (empty($request->input('wuliu_num'))){
                return ['status' => 'fail', 'msg' => '物流单号不能为空'];
            }
            $res = \DB::table('order_info')->where('id', $id)->update($request->only('wuliu_gongsi', 'wuliu_num'));
            if ($res >= 0){
                return ['status' => 'success', 'msg' => '修改成功'];
            }else{
                return ['status' => 'fail', 'msg' => '修改失败'];
            }
        }
        $wuliu = \DB::table('logistics')->get();
        $wuliu_info = \DB::table('order_info')->select('wuliu_gongsi', 'wuliu_num')->find($id);
//        dd($wuliu_info);

        return view('admin.order.edit_wuliu')->with(compact('wuliu', 'id', 'wuliu_info'));
    }

    /**
     * 发货
     * @param $request
     * @param $id
     * @return array
     * @author: simayubo
     */
    protected function doFahuo($request, $id){
        $order_info = \DB::table('order_info')->find($id);
        if ($order_info->status != 1){
            return ['status' => 'fail', 'msg' => '当前状态不能发货！'];
        }
        //查所有子订单
        $order_goods_list = \DB::table('order_goods')->select('status')->where('order_id', $id)->get();
        foreach ($order_goods_list as $item) {
            if ($item->status == 9){
                return ['status' => 'fail', 'msg' => '存在退款商品，请先处理！'];
            }
        }
        $time = Carbon::now();
        $data = $request->only('wuliu_gongsi', 'wuliu_num');
        $data['status'] = 2;
        $data['fahuo_time'] = $time;

        \DB::beginTransaction();
        //更新大订单
        $res = \DB::table('order_info')->where('id', $id)->update($data);
        //更新小订单
        $res1 = \DB::table('order_goods')->where(['order_id' => $id, 'status' => 1])->update(['status' => 2, 'fahuo_time' => $time]);

        if ($res && $res1){
            \DB::commit();
            $wechat = new WechatController();
            $wechat->sendTemplateMessage($id, 2);
            return ['status' => 'success', 'msg' => '发货成功！'];
        }else{
            \DB::rollBack();
            return ['status' => 'fail', 'msg' => '发货失败！'];
        }
    }

}
