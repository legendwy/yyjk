<?php



namespace App\Http\Controllers\Admin;



use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use Cache;

use DB;



class IndexController extends Controller

{

    public function index(){



        $user_list = $this->getNewUsers(6);

        $count = $this->getCount();



        return view('admin.index')->with(compact('user_list', 'count'));

    }

    public function test(){

        phpinfo();

    }



    /**

     * 获取最新用户

     * @param $num

     * @return \Illuminate\Support\Collection

     * @author: simayubo

     */

    protected function getNewUsers($num){

        return DB::table('users')->orderBy('id', 'desc')->limit($num)->get();

    }



    /**

     * 简单统计

     * @author: simayubo

     */

    protected function getCount(){

        return [

            'user'  =>  DB::table('users')->count('id'),

            'order' =>  DB::table('order_info')->where('status', '>', -2)->count('id'),

            'goods' =>  DB::table('goods')->where('status', '>', -2)->count('id'),

            'comment' => DB::table('commit')->count('id')

        ];

    }

    /**

     * 获取订单统计

     * @author: simayubo

     */

    public function getOrderCount(){

        $sql ="select a.click_date, ifnull(b.click_qty, 0) AS count

from (

    select * from (

            SELECT date_sub(curdate(), interval 9 day) as click_date

			union all

			SELECT date_sub(curdate(), interval 8 day) as click_date

			union all

			SELECT date_sub(curdate(), interval 7 day) as click_date

			union all

			SELECT date_sub(curdate(), interval 6 day) as click_date

			union all

			SELECT date_sub(curdate(), interval 5 day) as click_date

			union all

			SELECT date_sub(curdate(), interval 4 day) as click_date

			union all

			SELECT date_sub(curdate(), interval 3 day) as click_date

			union all

			SELECT date_sub(curdate(), interval 2 day) as click_date

			union all

			SELECT date_sub(curdate(), interval 1 day) as click_date

			union all

			SELECT curdate() as click_date

    ) c

) a left join (

  select DATE_FORMAT(add_time, '%Y-%m-%d') as click_date, count(*) as click_qty

  from order_info WHERE DATE_SUB(CURDATE(), INTERVAL 15 DAY) <= DATE_FORMAT(add_time, '%Y-%m-%d') AND status > -2 

  group by DATE_FORMAT(add_time, '%Y-%m-%d')

) b on a.click_date = b.click_date ORDER BY click_date ASC 

        ";

        $sum_order = DB::select($sql);



        $sql2 ="select a.click_date, ifnull(b.click_qty, 0) AS count

from (

    select * from (

            SELECT date_sub(curdate(), interval 9 day) as click_date

			union all

			SELECT date_sub(curdate(), interval 8 day) as click_date

			union all

			SELECT date_sub(curdate(), interval 7 day) as click_date

			union all

			SELECT date_sub(curdate(), interval 6 day) as click_date

			union all

			SELECT date_sub(curdate(), interval 5 day) as click_date

			union all

			SELECT date_sub(curdate(), interval 4 day) as click_date

			union all

			SELECT date_sub(curdate(), interval 3 day) as click_date

			union all

			SELECT date_sub(curdate(), interval 2 day) as click_date

			union all

			SELECT date_sub(curdate(), interval 1 day) as click_date

			union all

			SELECT curdate() as click_date

    ) c

) a left join (

  select DATE_FORMAT(add_time, '%Y-%m-%d') as click_date, count(*) as click_qty

  from order_info WHERE DATE_SUB(CURDATE(), INTERVAL 15 DAY) <= DATE_FORMAT(add_time, '%Y-%m-%d') AND status > -2 AND pay_status = 1

  group by DATE_FORMAT(add_time, '%Y-%m-%d')

) b on a.click_date = b.click_date

        ";

        $pay_order = DB::select($sql2);



        foreach ($sum_order as $k => &$v) {

            foreach ($pay_order as $_k => $_v) {

                if ($v->click_date == $_v->click_date){

                    $v->pay = $_v->count;

                }

            }

        }

        unset($v);



        return $sum_order;

    }



}

