<?php

namespace App\Http\Controllers\Api\v1_0;

use App\Http\Controllers\Api\BaseController;
use App\Models\Withdraw;
use App\Repositories\Eloquent\WithdrawRepository;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class WithdrawController extends BaseController
{
    protected $withdraw;

    public function __construct(WithdrawRepository $withdraw){
        $this->withdraw = $withdraw;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * 用户收益
     * @return mixed
     * @author fangweibo
     */
    public function userIncome()
    {
        $userInfo = JWTAuth::toUser();
        $income = \DB::table('rebate_record')->where('user_id',$userInfo['id'])->sum('credit');
//        $income = $userInfo->wallet;

        return $this->returnMsg(true,0,'查询成功',['money'=>$income]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $userInfo = JWTAuth::toUser();
        $credentials = $request->all();

        //获取后台设置的最低提现标准,钱包金额低于该标准不能提现
        $least = \DB::table('config')->where('id',19)->value('value');
        if($credentials['type'] == 2){ //type=2银行卡提现
            $rule = [
                'name'=>'required',
                'phone'=>'required',
                'bank'=>'required',
                'card'=>'required'
            ];
            $message = [
                'name.required'=>'开户人姓名不能为空',
                'phone.required'=>'联系电话不能为空',
                'bank.required'=>'开户银行不能为空',
                'card.required'=>'银行卡号不能为空',
            ];
        }
        $rule['money'] = 'required';
        $message['money.required'] = '提现金额不能为空';

        $validate = \Validator::make($credentials, $rule, $message);
        if ($validate->fails()) {
            return $this->returnMsg(false, 422, '数据验证失败', $validate->errors(), 422);
        }

        //提现金额不能大于钱包余额
        if($userInfo['wallet']<$credentials['money']){
            return $this->returnMsg(false,101,'余额不足',[]);
        };

        //提现金额不能小于最低提现标准
        if($credentials['money']<$least){
            return $this->returnMsg(false,102,'最低提现金额为'.$least.'元',[]);
        }

        $result = $this->withdraw->addWithdraw($credentials,$userInfo);

        if($result){
            return $this->returnMsg(true,0,'提交成功,等待后台处理');
        }else{
            return $this->returnMsg(false,422,'提交失败',[],422);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
