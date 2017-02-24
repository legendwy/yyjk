<?php

namespace App\Http\Controllers\Api\v1_0;

use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\WechatController;
use App\Models\UserGainsLogs;
use App\Repositories\Eloquent\OrderRepository;
use App\User;
use App\Models\Rebate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use App\Repositories\Eloquent\UserRepository;
use Illuminate\Support\Facades\DB;
use JWTAuth;
use Illuminate\Support\Facades\Session;

class UserController extends BaseController
{
    protected $user;

    public function __construct(UserRepository $user)
    {
        $this->user = $user;
    }

    /**
     * 获取所有用户
     * @return mixed
     */
    public function index(Request $request)
    {
        $users = $this->user->getUserList($request);
        return $this->returnMsg(true, 0, 'success', $users);
    }

    /**
     * 获取个人信息
     * User: lf
     * @param Request $request
     * @return mixed
     */
    public function getUserInfo(Request $request)
    {
        $userInfo = JWTAuth::toUser(JWTAuth::getToken());
        if (!preg_match("/^(http:\/\/).*$/", $userInfo->headimgurl)) {
            $userInfo->headimgurl = 'http://' . $_SERVER['HTTP_HOST'] . $userInfo->headimgurl;
        }
        return $this->returnMsg(true, 0, 'success', $userInfo);
    }

    /**
     * 修改密码
     * @param Request $request
     * @author lf
     */
    public function update(Request $request)
    {
        $credentials = $request->only('phone', 'ordpwd', 'password', 'password_confirmation');
        $validate = \Validator::make($credentials, [
            'phone' => 'required',
            'ordpwd' => 'required',
            'password' => 'required|confirmed',
            'password_confirmation' => 'required',
        ], [
            'phone' => '手机号不能为空',
            'ordpwd.required' => '原密码不能为空',
            'password.required' => '新密码不能为空',
            'password.password' => '密码不一致',
            'password_confirmation.required' => '确认密码不能为空',
        ]);
        if ($validate->fails()) {
            return $this->returnMsg(false, 422, '数据验证不通过', ['error' => $validate->errors()], 422);
        }

        $user_info = User::where('phone', $credentials['phone'])->first();
        $is_check = \Hash::check($credentials['ordpwd'], $user_info->password);
        if (!$is_check) {
            return $this->returnMsg('false', 422, '数据验证不通过', ['error' => '密码错误！'], 422);
        }
        $request['id'] = $user_info->id;
        $res = $this->user->updatePwd($request);
        return $this->returnMsg($res['success'], $res['code'], $res['msg'], $res['data'], $res['status']);
    }


    /**
     * 修改支付密码
     * @param Request $request
     * @author lf
     */
    public function savePayPwd(Request $request)
    {
        $credentials = $request->only('old_phone', 'pay_pwd', 'code');
        $validate = \Validator::make($credentials, [
            'old_phone' => 'required',
            'pay_pwd' => 'required|numeric|digits_between:6,6',
            'code' => 'required',
        ], [
            'old_phone.required' => '手机号不能为空',
            'pay_pwd.required' => '支付密码不能为空',
            'pay_pwd.numeric' => '支付密码必须为纯数字',
            'pay_pwd.between' => '支付密码必须为6位',
            'code.required' => '验证码不能为空',
        ]);
        if ($validate->fails()) {
            return $this->returnMsg(false, 422, '数据验证不通过', ['error' => $validate->errors()], 422);
        }

        $res = $this->checkCode($request);
        if (!$res['success']) {
            return ['success' => false, 'code' => 422, 'msg' => '验证码不正确', 'data' => '', 'status' => 422];
        }

        $user_info = User::where('phone', $credentials['old_phone'])->first();
        if (DB::table('users')->where('id', $user_info->id)->update(['pay_pwd' => bcrypt($request->input('pay_pwd'))])) {
            return ['success' => true, 'code' => 200, 'msg' => '设置成功', 'data' => '', 'status' => 200];
        } else {
            return ['success' => false, 'code' => 400, 'msg' => '设置失败', 'data' => '', 'status' => 422];
        }
    }

    /**
     * 验证验证码
     * User: lf
     * @param Request $request
     * @return array
     */
    public function checkCode(Request $request)
    {
        $user = JWTAuth::toUser();
        if ($request->input('type') == 1) {
            $phone = $request->input('old_phone');
        } else {
            $phone = $request->input('new_phone');
        }
        $msg_info = DB::table('msg_code')->where(['phone' => $phone, 'code' => $request->input('code')])->first();
        if (!$msg_info || (time() - $msg_info->time) > 600) {
            return ['success' => false, 'code' => 422, 'msg' => '验证码不正确', 'data' => '', 'status' => 422];
        }
        if ($request->input('type') == 2) {
            if (!$res = DB::table('users')->where('id', $user->id)->update(['phone' => $phone])) {
                return ['success' => true, 'code' => 400, 'msg' => '重置手机失败', 'data' => '', 'status' => 400];
            }
        }
        return ['success' => true, 'code' => 200, 'msg' => 'true', 'data' => '', 'status' => 200];
    }

    /**
     * 手机号获取验证码
     * User: lf
     * @param Request $request
     * @return array
     */
    public function getPhoneCode(Request $request)
    {
        $status = 1;
        $msg = '手机号不正确';
        $user = JWTAuth::toUser();
        if ($request->input('type') == 1) {//原手机号
            if (($phone = $user->phone) != $request->input('old_phone')) {
                $status = 0;
            }
        } else {
            $phone = $request->input('new_phone');
            if (!preg_match('/^1[34578]{1}\d{9}$/', $phone)) {
                $status = 0;
            }
            if ($user->phone == $phone) {
                $msg = "不能与原手机号相同";
                $status = 0;
            }
            $user_info = DB::table('users')->where([
                ['phone', '=', $phone],
                ['id', '<>', $user->id],
            ])->first();
            if (!empty($user_info)) {
                $msg = "该手机号已绑定过微信号了";
                $status = 0;
            }

        }
        if (!$status) {
            return ['success' => false, 'code' => 422, 'msg' => $msg, 'data' => '', 'status' => 422];
        }
        $home = new HomeController();
        $code = rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9);
        if ($home->getPhone($phone, $user->name, $code)) {
            DB::table('msg_code')->where('time', '<', time() - 3600 * 24)->delete();
            DB::table('msg_code')->insert(['phone' => $phone, 'code' => $code, 'time' => time()]);
            return ['success' => true, 'code' => 200, 'msg' => 'true', 'data' => '', 'status' => 200];
        } else {
            return ['success' => false, 'code' => 401, 'msg' => '发送失败', 'data' => '', 'status' => 401];
        }

    }

    /**
     * 用户云粉
     * User: lf
     * @return array
     */
    public function myChild()
    {
        $user_info = JWTAuth::toUser();
        $users_list = DB::table('users')->select('id', 'nickname', 'name', 'created_at', 'headimgurl', 'pid')->get();
        $childs_list = getChildsList($users_list->toArray(), $user_info->id);
        foreach ($childs_list as $k => $v) {
            $source_ids = [];
            $source_ids[] = $v['id'];
            if (!empty($v['child'])) {
                $source_ids[] = $v['child']['id'];
                $childs_list[$k]['count'] = count($v['child'], 1) / 7;
            } else {
                $childs_list[$k]['count'] = 0;
            };
            $credit = DB::table('rebate_record')->where('user_id', $user_info->id)->whereIn('source_id', $source_ids)->sum('credit');
            $childs_list[$k]['money'] = $credit;
        }
        return $this->returnMsg(true, 0, 'success', $childs_list);
    }

    /**
     * 用户二维码
     * User: lf
     * @return mixed
     */
    public function getQrcode()
    {
        $user_info = JWTAuth::toUser(JWTAuth::getToken());
        $new_user_info = DB::table('users')->where('id', $user_info->id)->first();
        if (substr($new_user_info->headimgurl, 0, 7) != 'http://') {
            $new_user_info->headimgurl = 'http://' . $_SERVER['HTTP_HOST'] . $new_user_info->headimgurl;
        }
        if (!$new_user_info->qrcode) {
            $wechat = new WechatController();
            $new_user_info->qrcode = $wechat->webQRCode($user_info->id);
        }
        $data = [
            'ID' => $new_user_info->id,
            'name' => $new_user_info->name,
            'headimgurl' => $new_user_info->headimgurl,
            'qrcode' => 'http://' . $_SERVER['HTTP_HOST'] . $new_user_info->qrcode];
        if ($new_user_info->qrcode) {
            return $this->returnMsg(true, 200, 'success', $data);
        } else {
            return $this->returnMsg(true, 200, '消费一次即可生成二维码', $data);
        }
    }

    /**
     * 修改用户信息
     * @param Request $request
     * @param $id
     * @return mixed
     * @author fangweibo
     */
    public function updateUserInfo(Request $request)
    {
        $credentials = $request->all();
//        return $credentials;
        $validate = \Validator::make($credentials, [
            'name' => 'required',
        ], [
            'name.required' => '姓名不能为空',
        ]);

        if ($validate->fails()) {
            return $this->returnMsg(false, 422, '数据验证不通过', ['error' => $validate->errors()], 422);
        }
        $user = JWTAuth::toUser();
        $res = $this->user->updateUserInfo($request, $user->id);
        return $this->returnMsg($res['success'], $res['code'], $res['msg'], $res['data'], $res['status']);
    }

    /**
     * 获取用户的收货地址列表
     * @param $id
     * @return mixed
     * @author fangweibo
     */
    public function addressList(Request $request)
    {
        $userInfo = JWTAuth::toUser(JWTAuth::getToken());
//        $address = User::findOrFail($userInfo['id'])->address;

        $address = $userInfo->address;
        if (!empty($address)) {
            foreach ($address as $k => $v) {
                if ($v['province']) {
                    $address[$k]['province'] = get_city_name($v['province']); //省
                }
                if ($v['city']) {
                    $address[$k]['city'] = get_city_name($v['city']); //市
                }
                if ($v['area']) {
                    $address[$k]['area'] = get_city_name($v['area']); //区
                }
            }
            return $this->returnMsg(true, 0, 'success', $address->toArray());
        } else {
            return $this->returnMsg(false, 422, '查询失败', [], 422);
        }
    }

    /**
     * 用户中心->提交意见反馈
     * @param Request $request
     * @return mixed
     * @author fangweibo
     */
    public function feedBack(Request $request)
    {
        $credentials = $request->all();
        $validate = \Validator::make($credentials, [
            'tel' => array('required', 'numeric', 'regex:/^1[34578][0-9]{9}$/'),
            'content' => 'required',
        ], [
            'tel.required' => '手机号不能为空',
            'tel.numeric' => '手机号必须是数字',
            'tel.regex' => '手机格式不正确',
            'content.required' => '反馈内容不能为空',
        ]);

        if ($validate->fails()) {
            return $this->returnMsg(false, 422, '数据验证不通过', ['error' => $validate->errors()], 422);
        }
        $userInfo = JWTAuth::toUser();
        $data = $request->all();
        $data['user_id'] = $userInfo->id;
        $data['created_at'] = Carbon::now();
        $data['updated_at'] = Carbon::now();
        //return $this->returnMsg(true, 0, '反馈成功',$data);

        $res = \DB::table('feedback')->insert($data);
//        $feedback = new FeedBack();
//        $feedback->tel = $request->tel;
//        $feedback->content = $request->content;
//        $feedback->openid = $userInfo->id;
//        return $feedback;
//        $res = $feedback->save();
        if ($res) {
            return $this->returnMsg(true, 0, '反馈成功');
        } else {
            return $this->returnMsg(false, 101, '提交失败', []);
        }
    }

    /**
     * 购物车列表
     * @Author wangyan
     */
    public function shopcar()
    {
        $list = $this->user->shopcar();
        if ($list) {
            return $this->returnMsg(true, 0, 'success', $list);
        } else {
            return $this->returnMsg(false, 1001, '暂无商品');
        }
    }

    /**
     * @Author wangyan
     */
    public function destroyCarById(Request $request)
    {
        $id = $request->id;
        if (empty($id)) {
            return $this->returnMsg(false, 1001, '缺少参数id(购物车id)');
        }
        $result = $this->user->destroyCarById($id);
        if ($result) {
            return $this->returnMsg(true, 0, '删除成功');
        } else {
            return $this->returnMsg(false, 1002, '删除失败');
        }
    }

    /**
     * 消费记录
     * @param Request $request
     * @return mixed
     * @author fangweibo
     */
    public function consumeDetail(Request $request)
    {
        $userInfo = JWTAuth::toUser();
//        return $userInfo;
//        $consume = $userInfo->consume->toArray();
        $consume = \DB::table('consumption_record')
            ->where('user_id', $userInfo->id)
            ->orderBy('id', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        if (!empty($consume)) {
            return $this->returnMsg(true, 0, 'success', $consume);
        } else {
            return $this->returnMsg(false, 101, '暂无记录', []);
        }
    }

    /**
     * 收益明细
     * @return mixed
     * @author fangweibo
     */
    public function rebate()
    {
        $userInfo = JWTAuth::toUser();
//        $rebate = $userInfo->rebate->toArray();
        $rebate = Rebate::where('user_id', $userInfo->id)
            ->orderBy('id', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
//        return $rebate;
        if (!empty($rebate)) {
            foreach ($rebate as $k => $v) {
                $user = User::findOrFail($v->source_id);
                if (!$user->nickname) {
                    $rebate[$k]->name = $user->nickname;
                } else {
                    $rebate[$k]->name = $user->name;
                }
//                return $user->headimgurl;
                if ($user->headimgurl) {
                    if (!preg_match("/^(http:\/\/).*$/", $user->headimgurl)) {
                        $rebate[$k]->headimgurl = 'http://' . $_SERVER['HTTP_HOST'] . $user->headimgurl;
                    } else {
                        $rebate[$k]->headimgurl = $user->headimgurl;
                    }
                } else {
                    $rebate[$k]->headimgurl = '';
                }
            }

            return $this->returnMsg(true, 0, 'success', $rebate);
        } else {
            return $this->returnMsg(false, 101, '暂无记录', []);
        }
    }

    /**
     * 购物车统计
     * @return mixed
     * @author: simayubo
     */
    public function getShopcarCount()
    {
        $userInfo = JWTAuth::toUser();
        $count = \DB::table('shopping_cart')->where('user_id', $userInfo->id)->count();
        return $this->returnMsg(true, 0, 'success', ['count' => $count]);
    }

    /**
     * 统计
     * @param OrderRepository $order
     * @return mixed
     * @author: simayubo
     */
    public function getOrderCount(OrderRepository $order)
    {
        $userInfo = JWTAuth::toUser();
        $user_id = $userInfo->id;

        return $this->returnMsg(true, 0, 'success', [
            'no_pay' => $order->getCountByStatus(-1, $user_id),
            'fahuo' => $order->getCountByStatus(1, $user_id),
            'shouhuo' => $order->getCountByStatus(2, $user_id),
            'pingjia' => $order->getCountByStatus(3, $user_id),
            'shouhou' => $order->getCountByStatus(-99, $user_id)
        ]);
    }
}
