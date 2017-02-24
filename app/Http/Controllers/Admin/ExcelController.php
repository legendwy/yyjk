<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\Eloquent\OrderRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Excel;

class ExcelController extends Controller
{
    public function exportOrder(Request $request, OrderRepository $order){

        $cellData = $this->getOrder($request,  $order);
//        dd($cellData);
        Excel::create(Carbon::now().'订单列表',function($excel) use ($cellData){
            $excel->sheet('score', function($sheet) use ($cellData){
                $sheet->rows($cellData);
            });
        })->export('xls');
    }

    /**
     * 获取订单
     * @param $request
     * @param $order
     * @return array
     * @author: simayubo
     */
    protected function getOrder($request, $order){
        $order_list = $order->getOrderInfoList($request, 'no')->toArray();

        $data[] = ['订单号', '购买用户', '商品列表', '商品价格', '邮费', '总价', '支付状态', '支付方式', '收货信息', '下单时间', '状态'];

        foreach ($order_list as $key => $value) {
            $col[0] = $value['order_num'];
            $col[1] = $value['phone'];
            $list = \DB::table('order_goods')->where([['order_id', '=', $value['id']], ['status', '>', -2]])->get();
            $_col = '';
            foreach ($list as $item) {
                $status_text = '';
                switch ($value['status']){
                    case -1: $status_text = '未支付'; break;
                    case 1: $status_text = '待发货'; break;
                    case 2: $status_text = '已发货'; break;
                    case 3: $status_text = '已确认收货'; break;
                    case 4: $status_text = '已完成'; break;
                    case 5: $status_text = '已取消'; break;
                    case 6: $status_text = '申请退货'; break;
                    case 7: $status_text = '退货中'; break;
                    case 8: $status_text = '退货完成'; break;
                    case 9: $status_text = '申请退款'; break;
                    case 10: $status_text = '同意退款'; break;
                }
                $_col .= $item->goods_name."【规格：".$item->goods_attr_values."】【状态：".$status_text."】\n";
            }
            $col[2] = $_col;

            $col[3] = $value['price'];
            $col[4] = $value['postage'];
            $col[5] = $value['price'] + $value['postage'];
            $col[6] = $value['pay_status'] == 1? '已支付': '未支付';
            if ($value['pay_status'] == 1){
                switch ($value['pay_type']){
                    case 1: $col[7] = '余额'; break;
                    case 2: $col[7] = '微信'; break;
                }
            }else{
                $col[7] = '--';
            }
            $col[8] = "城市：".$value['province']."，".$value['city']."，".$value['area']."；\n详细地址：".$value['addr']."；\n姓名：".$value['name']."；\n联系电话：".$value['phone']."";
            $col[9] = $value['add_time'];
            switch ($value['status']){
                case -1: $col[10] = '未支付'; break;
                case 1: $col[10] = '待发货'; break;
                case 2: $col[10] = '已发货'; break;
                case 3: $col[10] = '已确认收货'; break;
                case 4: $col[10] = '已完成'; break;
                case 5: $col[10] = '已取消'; break;
                case 6: $col[10] = '已关闭'; break;
            }
            $data[] = $col;
        }
        return $data;
    }
}
