<?php

namespace App\Http\Controllers\Api\v1_0;

use App\Http\Controllers\Api\BaseController;
use App\Http\Controllers\Comment\DistributionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use JWTAuth;
use Carbon\Carbon;
use App\Repositories\Eloquent\OrderRepository;

class OrderController extends BaseController
{
    private $user_info;
    private $orderRepository;

    public function __construct(OrderRepository $order)
    {
//        $this->getUsera();
        $this->orderRepository = $order;
    }

    protected function getUser()
    {
        $this->user_info = JWTAuth::toUser();
    }

    /**
     * 用户订单列表
     * User: lf
     * @param Request $request
     * @return mixed
     */
    public function getOrderList(Request $request)
    {
        $this->getUser();
        $where['oi.user_id'] = $this->user_info->id;
        $where[] = ['oi.status', '<>', -2];
        $where['og.user_id'] = $this->user_info->id;
        if ($request->get('oi_id')) $where['oi.id'] = $request->get('oi_id');
        if ($request->get('order_num')) $where['oi.order_num'] = $request->get('order_num');
        if ($request->get('big_status')) $where['oi.status'] = $request->get('big_status');
        if ($request->get('is_commit')) $where['oi.is_commit'] = $request->get('is_commit');
        if ($request->get('is_tui')) $where['oi.is_tui'] = $request->get('is_tui');
        $list = $tmplist = DB::table('order_info as oi')
            ->join('order_goods as og', 'oi.id', '=', 'og.order_id')
            ->leftJoin('logistics', 'oi.wuliu_gongsi', '=', 'logistics.id')
            ->leftJoin('region as province', 'province.REGION_ID', '=', 'oi.province')
            ->leftJoin('region as city', 'city.REGION_ID', '=', 'oi.city')
            ->leftJoin('region as area', 'area.REGION_ID', '=', 'oi.area')
            ->where($where)
            ->select('oi.id as oi_id', 'oi.order_num', 'oi.price as prices', 'oi.pay_status as big_pay_status', 'oi.status as big_status', 'oi.pay_price',
                'oi.add_time', 'oi.postage as big_postage', 'oi.pay_time', 'oi.fahuo_time', 'oi.shou_time', 'logistics.name as wuliu_gongsi', 'oi.wuliu_num', 'oi.remark',
                'province.REGION_NAME as province', 'city.REGION_NAME as city', 'area.REGION_NAME as area', 'oi.addr', 'oi.phone', 'oi.name',
                'og.id as og_id', 'og.order_id', 'og.goods_id', 'og.order_goods_number', 'og.price', 'og.num', 'og.goods_name', 'og.goods_thumb', 'og.goods_attr_values',
                'og.postage as small_postage', 'og.pay_status as small_pay_status', 'og.status as small_status', 'og.is_commit')
            ->orderBy('og.order_id', 'desc')
            ->paginate(18)->toArray();
        $order_list = [];
        foreach ($list['data'] as $k => $v) {
            $tmp_oi_id[] = 0;
            if (!array_search($v->oi_id, $tmp_oi_id)) {
                $v = get_object_vars($v);
                unset($v['goods_id'], $v['order_goods_number'], $v['price'], $v['num'], $v['goods_name'],
                    $v['goods_thumb'], $v['goods_attr_values'], $v['goods_attr_id'], $v['order_id'], $v['small_postage'],
                    $v['small_pay_status'], $v['small_status'], $v['is_commit']);
                foreach ($tmplist['data'] as $kk => $vv) {
                    $tmp_og_id = null;
                    $tmp_og_id[] = 0;
                    $vv = get_object_vars($vv);
                    if ($v['oi_id'] == $vv['order_id'] && !array_search($vv['og_id'], $tmp_og_id)) {
                        unset($vv['oi_id'], $vv['order_num'], $vv['prices'], $vv['big_pay_status'], $vv['big_status'],
                            $vv['pay_price'], $vv['add_time'], $vv['pay_time'], $vv['fahuo_time'], $vv['shou_time'],
                            $vv['wuliu_gongsi'], $vv['wuliu_num'], $vv['province'], $vv['city'], $vv['area'], $vv['addr'],
                            $vv['phone'], $vv['name'], $vv['remark'], $vv['big_postage']);
                        $v['items'][] = $vv;
                        $tmp_og_id[] = $vv['og_id'];
                    }
                }
                $v['count'] = count($v['items']);
                $v['page'] = count($v['items']);
                $order_list[$k] = $v;
                $tmp_oi_id[] = $v['oi_id'];
            }
        }
        unset($list['data']);
        $order_list_page['order_list'] = $order_list;
        $order_list_page['items'] = $list;
        return $this->returnMsg(true, 200, 'success', $order_list_page);
    }

    /**
     * 改变订单状态
     * User: lf
     * @param $oi_id 订单ID
     * @param $og_id 商品详情ID
     * @param $status 订单状态
     * @return mixed
     */
    public function changeStatus(Request $request, $id, $status)
    {
        $this->getUser();
        $arr_id = explode('_', $id);
        if ($arr_id[0] == 'oi') {
            $oi_id = $arr_id[1];
            $og_id = 0;
        } else if ($arr_id[0] == 'og') {
            $oi_id = 0;
            $og_id = $arr_id[1];
        } else {
            return false;
        }
        if ($oi_id) {
            $order_info = DB::table('order_info')->where(['id' => $oi_id, 'user_id' => $this->user_info->id])->first();
            $stauts = $order_info->status;
        }
        if ($og_id) {
            $order_goods_info = DB::table('order_goods')->where(['id' => $og_id, 'user_id' => $this->user_info->id])->first();
            $stauts = $order_goods_info->status;
        }
        $error_data = array('msg' => '操作失败', 'code' => 400);
        $success_data = array('msg' => '操作成功', 'code' => 200);
        switch ($status) {
            //取消订单
            case 5:
                if ($stauts != -1) {
                    $data = $error_data;
                    break;
                }
                DB::beginTransaction();
                $res = DB::table('order_info')->where(['id' => $oi_id, 'user_id' => $this->user_info->id])->update(['status' => 5]);
                $order_goods_list = DB::table('order_goods')->where(['order_id' => $oi_id, 'user_id' => $this->user_info->id])->get();
                foreach ($order_goods_list->toArray() as $v) {
                    $resss = DB::table('goods_attr')->where(['id' => $v->goods_attr_id])->increment('stock', $v->num);
                    if (!$resss) {
                        $data = $error_data;
                        break;
                    }
                }
                $ress = DB::table('order_goods')->where(['order_id' => $oi_id, 'user_id' => $this->user_info->id])->update(['status' => 5]);
                if ($res && $ress) {
                    DB::commit();
                    $data = $success_data;
                } else {
                    DB::rollback();
                    $data = $error_data;
                }
                break;
            //收货
            case 3:
                if ($stauts != 2) {
                    $data = $error_data;
                    break;
                }
                DB::beginTransaction();
                $all_count = DB::table('order_goods')->where(['order_id' => $oi_id, 'user_id' => $this->user_info->id])->count();
                $count = DB::table('order_goods')->where('status', 7)->where(['order_id' => $oi_id, 'user_id' => $this->user_info->id])->count();
                if ($all_count == $count) {
                    return $this->returnMsg(false, 200, '申请退货的商品不能收货！', '', 200);
                }
                $order_goods_list = DB::table('order_goods')->whereIn('status', [2, 6])->where(['order_id' => $oi_id, 'user_id' => $this->user_info->id])->get()->toArray();
                foreach ($order_goods_list as $k => &$v) {
                    $v = get_object_vars($v);
                    $sell_num = DB::table('goods')->where('id', $v['goods_id'])->increment('sell_num', $v['num']);
                    if (!$sell_num) {
                        DB::rollback();
                        $data = $error_data;
                    }
                }
                $og_ids = array_column($order_goods_list, 'id');
                $res = DB::table('order_goods')->whereIn('id', $og_ids)->update(['status' => 3, 'shou_time' => Carbon::now()]);
                $refund = DB::table('refund')->whereIn('order_id', $og_ids)->delete();
                $ress = DB::table('order_info')->where(['id' => $oi_id, 'user_id' => $this->user_info->id])->update(['status' => 3, 'shou_time' => Carbon::now()]);

                //返利
                $distribution = new DistributionController();
                $resss = $distribution->rebate($oi_id, $this->user_info->id);
                $resss = 1;
                if ($res && $ress && $resss && $sell_num && $refund >= 0) {
                    DB::commit();
                    $data = $success_data;
                } else {
                    DB::rollback();
                    $data = $error_data;
                }
                break;
            //评价
            case 4:
                if ($stauts != 3) {
                    $data = $error_data;
                    break;
                }
                if (empty($request->get('star')) || empty($request->get('content'))) {
                    return $this->returnMsg(false, 200, '星级或者内容不能为空！', '', 200);
                }
                DB::beginTransaction();
                $all_count = DB::table('order_goods')->whereIn('status', [3, 4, 8, 10])->where(['order_id' => $order_goods_info->order_id, 'user_id' => $this->user_info->id])->count();
                $count = DB::table('order_goods')->where(['order_id' => $order_goods_info->order_id, 'user_id' => $this->user_info->id])->whereIn('status', [4, 8, 10])->count();
                if ($all_count - $count == 1) {
                    $ress = DB::table('order_info')->where(['id' => $order_goods_info->order_id, 'user_id' => $this->user_info->id])->update(['is_commit' => 1, 'status' => 4]);
                } else {
                    $ress = 1;
                }
                $res = DB::table('order_goods')->where(['id' => $og_id, 'user_id' => $this->user_info->id])->update(['is_commit' => 1, 'status' => 4]);

                $ressss = DB::table('goods')->where(['id' => $order_goods_info->goods_id])->increment('count_comment');
                $input = [
                    'user_id' => $this->user_info->id,
                    'goods_id' => $order_goods_info->goods_id,
                    'order_id' => $og_id,
                    'star' => $request->get('star'),
                    'content' => $request->get('content'),
                    'pic' => $request->get('pic'),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
                $commit_id = DB::table('commit')->insertGetId($input);
                $star_avg = DB::table('commit')->where(['goods_id' => $order_goods_info->goods_id])->avg('star');
                $resssss = DB::table('goods')->where(['id' => $order_goods_info->goods_id])->update(['star' => ceil($star_avg)]);
                if ($res && $ress && $commit_id && $ressss && $resssss >= 0) {
                    DB::commit();
                    $data = $success_data;
                } else {
                    DB::rollback();
                    $data = $error_data;
                }
                break;
            //申请退货退款
            case 6:
                DB::beginTransaction();
                $type = $request->get('type');
                //退款,待发货；退货,已发货
                if (!($type == 2 && ($stauts == 2)) && !($type == 1 && $stauts == 1)) {
                    $data = $error_data;
                    break;
                }
                $refund_info = DB::table('refund')->where(['order_id' => $og_id, 'uid' => $this->user_info->id, 'type' => $type])->first();
                if (empty($request->get('reason_id')) || empty($type)) {
                    return $this->returnMsg(false, 422, '表单不能为空');
                }
                $input = [
                    'type' => $type,
                    'uid' => $this->user_info->id,
                    'order_id' => $og_id,
                    'reason_id' => $request->get('reason_id'),
                    'money' => $order_goods_info->price * $order_goods_info->num,
                    'content' => $request->get('content'),
                    'pic' => trim($request->get('pic'), '@'),
                    'time' => Carbon::now(),
                    'status' => 0,
                ];

                if ($refund_info) {
                    $refund_id = DB::table('refund')->where('id', $refund_info->id)->update($input);
                    $ress = 1;
                } else {
                    $refund_id = DB::table('refund')->insertGetId($input);
                    $ress = 1;
                }
                if ($type == 2) {
                    $res = DB::table('order_goods')->where(['id' => $og_id, 'user_id' => $this->user_info->id])->update(['status' => 6]);
                } else {
                    $res = DB::table('order_goods')->where(['id' => $og_id, 'user_id' => $this->user_info->id])->update(['status' => 9]);
                }


                if ($res && $ress && $refund_id) {
                    DB::commit();
                    $data = $success_data;
                } else {
                    DB::rollback();
                    $data = $error_data;
                }
                break;
            //订单删除
            case -2:
                $status = array(0, -1, 4, 5, 6);
                if (!array_search($order_info->status, $status)) {
                    $data = $error_data;
                    break;
                }
                DB::beginTransaction();
                $res = DB::table('order_info')->where(['id' => $oi_id, 'user_id' => $this->user_info->id])->update(['status' => -2]);
                $ress = DB::table('order_goods')->where(['order_id' => $oi_id, 'user_id' => $this->user_info->id])->update(['status' => -2]);
                if ($res && $ress) {
                    DB::commit();
                    $data = $success_data;
                } else {
                    DB::rollback();
                    $data = $error_data;
                }
                break;
            default:
                $data = $error_data;
                break;
        }

        if ($data['code'] == 400) {
            return $this->returnMsg(false, 400, '操作失败');
        } else {
            if (!empty($refund_id)) {//退货退款
                return $this->returnMsg(true, 200, '操作成功', ['refund_id' => $refund_id]);
            }
            return $this->returnMsg(true, 200, '操作成功');
        }
    }

    /**
     * 退货退款原因列表
     * User: lf
     * @return mixed
     */
    public function getReason()
    {
        $reason_list = DB::table('reason')->get();
        return $this->returnMsg(true, 200, 'success', $reason_list);
    }

    /**
     * 物流公司列表
     * User: lf
     * @return mixed
     */
    public function getWuliu()
    {
        $logistics_list = DB::table('logistics')->get();
        return $this->returnMsg(true, 200, 'success', $logistics_list);
    }

    /**
     * 取消退款退货
     * User: lf
     * @param $id 申请退货退款ID
     * @return \Dingo\Api\Dispatcher
     */
    public function refundDel($id)
    {
        $this->getUser();
        DB::beginTransaction();
        $refund_info = DB::table('refund')->where('id', $id)->first();
        $order_goods_info = DB::table('order_goods')->where('id', $refund_info->order_id)->first();
        $order_info = DB::table('order_info')->where('id', $order_goods_info->order_id)->first();
        $res = DB::table('order_goods')->where('id', $refund_info->order_id)->update(['status' => $order_info->status]);
        $ress = DB::table('refund')->where('id', $id)->delete();
        if ($res && $ress) {
            DB::commit();
            return $this->returnMsg(true, 200, '已取消');
        } else {
            DB::rollback();
            return $this->returnMsg(false, 200, '操作失败');
        }
    }

    /**
     * 上传图片
     * User: lf
     * @param Request $request
     * @return mixed
     */
    public function uploadImg(Request $request)
    {
        $res = upload_file($request->file('image'), 'refund', 'image');
        if ($res['status']) {
            return $this->returnMsg(true, 200, 'success', ['path' => $res['path'], 'del' => '/img/del.png']);
        } else {
            return $this->returnMsg(false, 200, 'false', ['error' => $res['error']]);
        }
    }

    /**
     * 删除图片
     * User: lf
     * @param $path 图片路径
     * @return mixed
     */
    public function delImg(Request $request)
    {
        if (!is_file('.' . $request->get('path'))) {
            return $this->returnMsg(false, 200, '删除失败');
        }
        if (unlink('.' . $request->get('path'))) {
            return $this->returnMsg(true, 200, '删除成功');
        } else {
            return $this->returnMsg(false, 200, '删除失败');
        }
    }

    /**
     * 物流查询
     * User: lf
     * @param $id 订单ID
     * @return mixed
     */
    public function wuLiu($id)
    {
        $this->getUser();
        $goods_count = DB::table('order_goods')->where(['order_id' => $id, 'user_id' => $this->user_info->id])->count();
        $wuliu = $this->orderRepository->getWuliuTracesByOrderId($id);
        if (!empty($wuliu['Traces'])) {
            $count = count($wuliu['Traces']) - 1;
            foreach ($wuliu['Traces'] as $k => $v) {
                $arr[$count - $k] = $v;
            }
            $wuliu['Traces'] = $arr;
        } else {
            $wuliu['Traces'] = '';
        }
        $wuliu['goods_count'] = $goods_count;
        return $this->returnMsg(true, 200, 'success', $wuliu);
    }

    /**
     * 退款退货售后
     * User: lf
     * @return mixed
     */
    public function refundList()
    {
        $this->getUser();
        $refund_list = DB::table('refund')
            ->leftJoin('order_goods', 'refund.order_id', '=', 'order_goods.id')
            ->where('refund.uid', $this->user_info->id)
            ->orderBy('refund.id', 'desc')
            ->select('refund.*', 'order_goods.goods_name', 'order_goods.num', 'order_goods.price', 'order_goods.goods_name', 'order_goods.postage', 'order_goods.goods_id as goods_id', 'order_goods.order_goods_number', 'order_goods.goods_attr_values', 'order_goods.goods_thumb')
            ->paginate(15)->toArray();
        foreach ($refund_list['data'] as $k => &$v) {
            $v = get_object_vars($v);
            $v['sum_price'] = $v['price'] * $v['num'];
        }
        $refund_list_page['order_list'] = $refund_list['data'];
        unset($refund_list['data']);
        $refund_list_page['page'] = $refund_list;
        return $this->returnMsg(true, 200, 'success', $refund_list_page);
    }

    /**
     * 填写退货信息
     * User: lf
     * @param Request $request
     * @return mixed
     */
    public function saveRefund(Request $request)
    {
        if (empty($request->input('company')) || empty($request->input('number'))) {
            return $this->returnMsg(false, 404, '物流公司或物流单号不能为空', 404);
        }
        $this->getUser();
        $input = [
            'company' => (int)$request->input('company'),
            'number' => $request->input('number'),
            'is_set' => 1
        ];

        $res = DB::table('refund')->where(['id' => (int)$request->input('id'), 'uid' => $this->user_info->id, 'status' => 2])->update($input);
        if ($res) {
            return $this->returnMsg(true, 200, 'success');
        } else {
            return $this->returnMsg(false, 400, 'false');
        }
    }

    //收货地址
    public function address()
    {
        $address = DB::table('config')->where('id', 11)->value();
        return $this->returnMsg(true, 200, 'success', $address);
    }
}
