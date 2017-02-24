<?php

namespace App\Http\Controllers\Api\v1_0;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use Illuminate\Support\Facades\DB;
use JWTAuth;


class AuthController extends BaseController
{
    /**
     * 注册用户
     * User: lf
     * @param Request $request
     * @return mixed
     */
    public function register(Request $request)
    {
        $credentials = $request->only('name', 'email', 'phone', 'p_phone', 'password');
        $validate = \Validator::make($credentials, [
            'name' => 'required|unique:users,name',
            'email' => 'required|unique:users,email',
//            'phone' => 'required|unique:users,phone',
            'password' => 'required'
        ], [
            'name.required' => '昵称不能为空',
            'email.required' => '邮箱不能为空',
//            'phone.required' => '手机不能为空',
            'password.required' => '密码不能为空',
            'name.unique' => '昵称已存在',
            'email.unique' => '邮箱已存在',
//            'phone.unique' => '手机已存在'
        ]);
        if ($validate->fails()) {
            return $this->returnMsg(false, 422, '数据验证不通过', ['error' => $validate->errors()], 422);
        }
        if ($p_userinfo = DB::table('users')->where('phone', $request->input('p_phone'))->first()) {
            $credentials['pid'] = $p_userinfo->id;
        }
        $credentials['password'] = bcrypt($credentials['password']);
        if ($user = User::create($credentials)) {
            $token = JWTAuth::fromUser($user);
            return $this->returnMsg(true, 0, '注册成功', ['token' => $token]);
        } else {
            return $this->returnMsg(false, 101, '注册失败', [], 500);
        }
    }

    /**
     * 登录账号
     * User: lf
     * @param Request $request
     * @return mixed
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password', 'phone');

        $validate = \Validator::make($credentials, [
            'email' => 'required',
            'password' => 'required',
//            'phone' => 'required'
        ], [
            'email.required' => '邮箱不能为空',
            'password.required' => '密码不能为空',
//            'phone.required' =>  '电话不能为空'
        ]);
        if ($validate->fails()) {
            return $this->returnMsg(false, 422, '数据验证不通过', ['error' => $validate->errors()], 422);
        }

        $user = User::where('email', $credentials['email'])->first();
        if ($user) {
            $is_check = \Hash::check($request->input('password'), $user->password);
            if ($is_check) {
                $token = JWTAuth::fromUser($user);
                return $this->returnMsg(true, 0, 'success', ['token' => $token]);
            }
        }
        return $this->returnMsg(false, 1001, '账号或密码错误', [], 200);
    }

    //测试获取token
    public function login2(){
        $user = User::where('id', 41)->first();
        $token = JWTAuth::fromUser($user);
        return $this->returnMsg(true, 0, 'success', ['token' => $token]);


    }

}