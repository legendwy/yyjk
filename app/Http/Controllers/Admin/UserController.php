<?php

namespace App\Http\Controllers\Admin;

use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Eloquent\UserRepository;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    protected $user;

    public function __construct(UserRepository $user)
    {
        $this->middleware('check.permission:user');
        $this->user = $user;
    }


    /**
     * 添加
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author: simayubo
     */
    public function create()
    {
        return view('admin.user.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * 编辑用户
     * @param $id
     * @author: simayubo
     */
    public function edit($id)
    {
        $user_info = User::find($id)->toArray();
        return view('admin.user.edit')->with(compact('user_info'));
    }

    /**
     * 更新用户信息
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @author: simayubo
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|unique:menus,name,' . $id . '',
            'email' => 'required|email|unique:menus,name,' . $id . '',
        ], [
            'name.required' => '用户名不能为空',
            'email.required' => '邮箱不能为空',
            'email.unique' => '邮箱已存在',
            'email.email' => '邮箱格式不正确',
            'name.unique' => '用户名已存在'
        ]);

        $this->user->updateUser($request, $id);
        return redirect('admin/user');
    }

    /**
     * 删除用户
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        $this->user->destoryUser($id);
        return redirect('admin/user');
    }

    /**
     * 禁用用户
     * User: lf
     * @param Request $request
     * @return bool
     */
    public function lock(Request $request)
    {
        return $this->user->lock($request);
    }

    /**
     * 用户收货地址
     * User: lf
     * @return UserRepository
     */
    public function getUserAddress(Request $request)
    {
        $address_list = $this->user->getUserAddress($request->get('user_id'));
        return view('admin.user.address', compact('address_list'));
    }

    /**
     * 用户列表
     * @param Request $request
     */
    public function index(Request $request)
    {
        $user_list = $this->user->getUserList($request);
        $users_list = DB::table('users')->select('id', 'nickname', 'name', 'created_at', 'pid')->get()->toArray();
        foreach ($user_list as $k => &$v) {
            $childs_list = getChildsList($users_list, $v->id);
            $v->count = count($childs_list, 1) / 7;
        }
        return view('admin.user.list')->with(compact('user_list'));
    }

    /**
     * 下级用户
     * User: lf
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function userChild(Request $request)
    {
        $users_list = DB::table('users')->select('id', 'nickname', 'name', 'created_at', 'pid')->get();
        $childs_list = getChildsList($users_list->toArray(), $request->get('user_id'));
        return view('admin.user.userChild', compact('childs_list'));
    }

    /**
     * 返利记录
     * User: lf
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function userFanli(Request $request)
    {
        $fanli_list = DB::table('rebate_record')
            ->join('users', 'rebate_record.source_id', '=', 'users.id')
            ->where('user_id', $request->get('user_id'))
            ->select('rebate_record.*', 'users.name', 'users.nickname')
            ->orderBy('id','desc')
            ->get();
        return view('admin.user.user_fanli', compact('fanli_list'));
    }

    /**
     * 余额记录
     * User: lf
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function userMingxi(Request $request)
    {
        $mingxi_list = DB::table('consumption_record')
            ->where('user_id', $request->get('user_id'))
            ->orderBy('id','desc')
            ->get();
        return view('admin.user.user_mingxi', compact('mingxi_list'));
    }
    /**
     * 获取省市区信息
     * @param $pid
     * @return mixed
     * @author fangweibo
     */
    public function area(Request $request)
    {
        $pid = $request->pid;
        if ($pid != 0) {
            $data = \DB::table('region')->where('parent_id', $pid)->select('REGION_ID', 'REGION_NAME')->get();
            if ($data) {
                return ['status' => 1, 'data' => $data];
            } else {
                return ['status' => 0];
            }
        }
    }


    /**
     * 用户代理列表页面
     * @param Request $request
     * @return int
     * @author fangweibo
     */

    public function agencySetList($id)
    {
        $user_daili = \DB::table('agency')->select('province', 'city', 'area', 'daili', 'created_at')
            ->where('user_id', $id)
            ->get();
        if ($user_daili) {
            foreach ($user_daili as $k => $v) {
                $user_daili[$k]->provinces = get_city_name($v->province);
                $user_daili[$k]->citys = get_city_name($v->city);
                $user_daili[$k]->areas = get_city_name($v->area);
                $user_daili->daili = $v->daili;
            }
        }
        return view('admin.user.agencyList')->with(compact('id', 'user_daili'));
    }

    /**
     * 设置代理
     * @param Request $request
     * @param $id //用户ID
     * @return $this|array
     * @author fangweibo
     */
    public function agencySet(Request $request, $id)
    {
        if ($request->isMethod('post')) {
            $input = $request->all();
            if (!$input['province']) {
                return ['status' => 0, 'msg' => '省级不能为空'];
            }
            if(!$request->daili){
                return ['status' => 0, 'msg' => '代理类型不能为空'];
            }
			$level = 1; //省市区个数,默认为1(省)
            //在数据库中根据请求中的代理类型和区域查找该区域是否已设置过该类型代理
            if(!$input['city'] && !$input['area']){ //市,区为空时,判断除自己之外其他用户是否为该代理
                $result = \DB::table('agency')->where([
                    ['province','=',$input['province']],
                ])->get()->toArray();
                if($result){
                    return ['status'=>0,'msg'=>'该区域已存在此代理'];
                }
            }elseif(!$input['area']){ //区为空时,判断除自己之外其他用户是否为该代理
                $level = 2; //省市区个数(省,市)
                $res1 = \DB::table('agency')
                    ->where([
                        ['city','=',$input['city']],
                        ['province','=',$input['province']]
                    ])
                    ->get()->toArray();
                if($res1){ //判断是否有该省市级代理
                    return ['status'=>0,'msg'=>'该区域已存在此代理'];
                }

                $result = \DB::table('agency')
                    ->where([
                        ['province','=',$input['province']],
                        ['city','=','']
                    ])
                    ->get()->toArray();
                if($result){ //判断是否有该省级的代理
                    return ['status'=>0,'msg'=>'该区域已存在此代理'];
                }
            }else{
                $level = 3; //省市区个数(省,市,区)
                $result = \DB::table('agency')
                    ->where([
                        ['area','=',$input['area']],
                        ['city','=',$input['city']],
                        ['province','=',$input['province']]
                    ])
                    ->get()->toArray();
                if($result){ //判断是否有该省市区级代理
                    return ['status'=>0,'msg'=>'该区域已存在此代理'];
                }

                $res1 = \DB::table('agency')
                    ->where([
                        ['area','=',''],
                        ['city','=',$input['city']],
                        ['province','=',$input['province']]
                    ])
                    ->get()->toArray();
                if($res1){ //判断该省市级是否有该区代理
                    return ['status'=>0,'msg'=>'该区域已存在此代理'];
                }

                $res2 = \DB::table('agency')
                    ->where([
                        ['city','=',''],
                        ['province','=',$input['province']],
                    ])
                    ->get()->toArray();
                if($res2){ //判断该省级代理是否存在
                    return ['msg' => '该区域已存在此代理', 'status' => 0];
                }
            }

            //每个用户只能擔任一種類型的代理
            $daili = \DB::table('users')->where('id', $id)->value('daili');
            if($daili != $request->daili && $daili != 1){
                return ['msg' => '用户只能担任一种类型的代理', 'status' => 0];
            }

            \DB::beginTransaction();
            //插入到代理(agency)表
            $agen = \DB::table('agency')
                ->insert([
                    'province' => $request->province,
                    'city' => $request->city,
                    'area' => $request->area,
                    'user_id' => $id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
					'level'=>$level,
                    'daili' => $request->daili,
                ]);
            //更新用户(users)表的代理类型(daili)
            if($daili ==1){
                $daili_type = \DB::table('users')->where('id', $id)
                    ->update(['daili' => $request->daili]);
            }else{
                $daili_type = true;
            }

            if ($agen && $daili_type) {
                \DB::commit();
                return ['msg' => '设置成功', 'status' => 1];
            } else {
                \DB::rollBack();
                return ['msg' => '设置失败', 'status' => 0];
            }
        }
        $province = \DB::table('region')->where('parent_id', 1)->get();
//        return $province;
        return view('admin.user.agencySet')->with(compact('province', 'id')); //省,用户ID
    }

    /**
     * 获取代理列表信息
     * @return $this
     * @author fangweibo
     */
    public function agencyList(Request $request)
    {
        $list = $this->user->getAllUserAgency($request);
//        dump($list);die;
        return view('admin.user.agency')->with(compact('list'));
    }

    /**
     * 取消代理
     * @param Request $request
     * @param $id
     * @return $this|array
     * @author fangweibo
     */
    public function agencyUndo(Request $request, $id)
    {
        if ($request->isMethod('post')) {
            $user_id = $request->user_id;
            \DB::beginTransaction();
            $res = \DB::table('agency')->where('id', $id)->delete();
            $res1 = \DB::table('agency')->where('user_id', $user_id)->get()->toArray();
            if (empty($res1)) { //代理表中没有该用户
                //将该用户的daili设置为1(不是代理)
                $res2 = \DB::table('users')->where('id', $user_id)->update(['daili'=> 1]);
            } else {
                $res2 = true;
            }

            if ($res && $res2) {
                \DB::commit();
                return ['msg' => '取消成功', 'status' => 1];
            } else {
                \DB::rollBack();
                return ['msg' => '取消失败', 'status' => 0];
            }
        }
        $agency_list = $this->user->getUserAgency($id);
//        dump($agency_list);die;
        return view('admin.user.agencyUndo')->with(compact('agency_list'));
    }		/**     * 代理返利明细     * @param Request $request     * @Author wangyan     */    public function agencyFanli(Request $request){        $user_id =  $request->user_id;        $fanli_list = DB::table('consumption_record')->where(['user_id' => $user_id, 'type' =>1])->get();        return view('admin.user.agency_fanli')->with(compact('fanli_list'));    }		
}
