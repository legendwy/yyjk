<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use DB;

class TipComposer
{
    public function compose(View $view)
    {
        $fahuo_order_count = $this->getFahuoOrder();
        $shouhou_order_count = $this->getShouhouOrder();

        $view->with(compact('fahuo_order_count', 'shouhou_order_count'));
    }
    /**
     * 获取待发货订单数量
     * @author: simayubo
     */
    private function getFahuoOrder(){
        return DB::table('order_info')->where(['status' => 1])->count('id');
    }

    /**
     * 获取待处理售后订单数量
     * @author: simayubo
     */
    private function getShouhouOrder(){
        return DB::table('refund')->where(['status' => 0])->count('id');
    }
}