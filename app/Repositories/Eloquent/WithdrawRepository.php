<?php
/**
 * Created by PhpStorm.
 * User: fangweibo
 * Date: 2016/12/26
 * Time: 17:15
 */

namespace App\Repositories\Eloquent;


use App\Models\Withdraw;
use Carbon\Carbon;

class WithdrawRepository extends Repository
{
    public function model()
    {
        return Withdraw::class;
    }

    /**
     * 提现申请
     * @param $request
     * @param $userInfo
     * @return bool
     * @author fangweibo
     */
    public function addWithdraw($request, $userInfo)
    {
        \DB::beginTransaction();
        //从收益中减去提现金额
        $surplus = $userInfo['wallet'] - $request['money']; //剩余钱包金额
        //保存到用户信息表
        $result = \DB::table('users')->where('id', $userInfo['id'])->update(['wallet' => $surplus]);
        //保存到消费明细表
        //$consume_id = \DB::table('consumption_record')->insert([
        //    'user_id' => $userInfo['id'],
//            'money' => $request['money'],
//            'surplus_money' => $surplus,
//            'use' => '提现',
//            'status' => '-1',
//            'created_at' => Carbon::now(),
//            'updated_at' => Carbon::now()
//        ]);
        //保存到提现表
        $data['user_id'] = $userInfo['id'];
        $data['money'] = $request['money'];
        $data['surplus_money'] = $surplus;
        if ($request['type'] == 2) {
            $data['name'] = $request['name'];
            $data['phone'] = $request['phone'];
            $data['bank'] = $request['bank'];
            $data['card'] = $request['card'];
        }
        $data['type'] = $request['type'];
        $data['created_at'] = Carbon::now();
        $data['updated_at'] = Carbon::now();
        $res = \DB::table('withdraw')->insert($data);

        if ($res && $consume_id && $result) {
            \DB::commit();
            return true;
        } else {
            \DB::rollBack();
            return false;
        }


    }

    /**
     * 提现申请列表
     * @param $request
     * @return \Illuminate\Support\Collection
     * @author fangweibo
     */
    public function withdrawList($request)
    {
        $input = $request->all();
        if (isset($input['status'])) {
            if ($input['status'] == 0) {
                unset($input['status']);
            } elseif ($input['status'] == -1) {
                $input['status'] = '0';
            }
        }
        $where = [];
        if (!empty($input['user_name'])) $where['users.name'] = $input['user_name'];
        if (isset($input['status'])) $where['withdraw.status'] = $input['status'];

        $list = \DB::table('withdraw')
            ->join('users', 'users.id', '=', 'withdraw.user_id')
            ->select('withdraw.*', 'users.name as user_name')
            ->where($where)			->orderBy('created_at', 'desc')
            ->get();
        return $list;
    }
}