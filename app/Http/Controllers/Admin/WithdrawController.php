<?php


namespace App\Http\Controllers\Admin;



use App\Http\Controllers\Web\WechatController;
use App\Models\Withdraw;

use App\Repositories\Eloquent\WithdrawRepository;

use Carbon\Carbon;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;



class WithdrawController extends Controller

{

    protected $withdraw;



    public function __construct(WithdrawRepository $withdraw){

        $this->withdraw = $withdraw;

    }



    /**

     * 提现申请列表

     * @return $this

     * @author fangweibo

     */

    public function index(Request $request)

    {

        $data = $this->withdraw->withdrawList($request);

//return $data;

        return view('admin.user.withdraw')->with(compact('data'));

    }



    /**

     * 处理提现申请

     * @param Request $request

     * @return int

     * @author fangweibo

     */

    public function deal(Request $request)

    {

        $status = $request->status;

        $id = $request->id;

//        return $request->all();

        $withdraw = Withdraw::find($id);
//        return $withdraw;
		\DB::beginTransaction();
        //保存到消费明细表
        $consume_id = \DB::table('consumption_record')->insert([
            'user_id' => $withdraw['id'],
            'money' => $withdraw['money'],
//            'surplus_money' => $withdraw['surplus_money'],
            'use' => '提现',
            'status' => '-1',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        $withdraw->status = $status;
        if ($withdraw->save() && $consume_id) {
            \DB::commit();
            $wechat = new WechatController();
            $wechat->sendTemplateMessage($id, 3);
            return 1;
        } else {
            \DB::rollBack();
            return 0;
        }

    }



    /**

     * 拒绝理由填写页面

     * @param Request $request

     * @return $this

     * @author fangweibo

     */

    public function refuse(Request $request)

    {

        $id = $request->get('id');

        return view('admin.user.refuse')->with(compact('id'));

    }



    /**

     * 拒绝提现理由提交

     * @param Request $request

     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|void

     * @author fangweibo

     */

    public function refuseReason(Request $request)

    {

        $this->validate($request,[

            'reason'=>'required',

        ],[

            'reason.required'=>'拒绝理由不能为空',

        ]);



        $id = $request->id;

        $data = Withdraw::find($id);

        $data->reason = $request->reason;

        if($data->save()){ //成功修改拒绝理由

            //将申请状态改为 2(已拒绝)

            $data->status = 2;

            $data->save();

            //将提现金额返回给用户

            $money = $data->money;

            \DB::table('users')->where('id',$data->user_id)->increment('wallet',$money);

            //更新消费明细表

//            \DB::table('consumption_record')->insert([

//                'user_id'=>$data->user_id,

//                'money'=>$money,

//                'use'=>'提现被拒返回资金',

//                'status'=>'1',

//                'created_at'=>Carbon::now(),

//                'updated_at'=>Carbon::now()

//            ]);


            $wechat = new WechatController();
            $wechat->sendTemplateMessage($id, 3);
        }

        return redirect('admin/withdraw');

    }



}

