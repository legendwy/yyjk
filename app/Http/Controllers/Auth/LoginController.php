<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }
    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            'name'     =>  'required',
            'password' => 'required',
            'captcha' => 'required|captcha'
        ],[
            'name.required' =>  '用户名不能为空',
            'captcha.required' => '验证码 不能为空',
            'captcha.captcha' => '验证码 错误',
        ]);
    }

}
