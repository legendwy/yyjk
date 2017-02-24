<?php
namespace App\Repositories\Eloquent;

use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use JWTAuth;

class UserRepository extends Repository
{
    public function model()
    {
        return User::class;
    }

    /**
     * 添加用户
     * @param $request
     * @return int
     */
    public function insertUser($request)
    {
        $user_id = $this->model->create($request);
        return $user_id;
    }

    /**
     * 获取用户列表
     * @param $request
     */
    public function getUserList($request)
    {
        $input = $request->all();
        $where = [];
        $orWhere = [];
        if (isset($input['status'])) {
            if ($input['status'] == 0) {
                unset($input['status']);
            } elseif ($input['status'] == -1) {
                $input['status'] = '0';
            }
        }

        if (!empty($input['name'])) $where[] = ['name', 'like', '%' . $input['name'] . '%'];
        if (!empty($input['name'])) $orWhere[] = ['nickname', 'like', '%' . $input['name'] . '%'];
        if (!empty($input['phone'])) $where[] = ['phone', '=', $input['phone']];
        if (!empty($input['id'])) $where[] = ['id', '=', $input['id']];
        if (isset($input['status'])) $where[] = ['status', '=', $input['status']];
        $list = $this
            ->model
            ->where($where)
            ->orWhere($orWhere)
            ->orderBy('id', 'desc')
            ->paginate(15);

        return $list;
    }

    /**
     * 禁用用户
     * User: lf
     * @param $request
     * @return bool
     */
    public function lock($request)
    {
        if (DB::table('users')->where('id', $request->input('id'))->update(['status' => $request->input('status')])) {
            return $request->input('status');
        } else {
            return false;
        }
    }

    /**
     * 用户收货地址
     * User: lf
     * @param $user_id
     * @return mixed
     */
    public function getUserAddress($user_id)
    {
        $data = DB::table('shipping_address')->where('user_id', $user_id)->get();
        if($data){
            foreach($data as $k=>$v){
                if($v->province){
                    $data[$k]->province = get_city_name($v->province); //省
                }
                if($v->city){
                    $data[$k]->city = get_city_name($v->city); //市
                }
                if($v->area){
                    $data[$k]->area = get_city_name($v->area); //区
                }
            }
        }

        return $data;
    }

    /**
     * 删除用户
     * @param $id
     * @return bool
     * @author: simayubo
     */
    public function destoryUser($id)
    {
        $isDelete = $this->model->destroy($id);
        if ($isDelete) {
            flash('会员删除成功！', 'success');
            return true;
        }
        flash('会员删除失败！', 'error');
        return false;
    }

    public function updateUser($request, $id)
    {
        $user = $this->model->find($id);
        $input = $request->all();
        if (!empty($input['password'])) {
            $input['password'] = bcrypt($input['password']);
        }
        $input['remember_token'] = Str::random(60);

        if ($user) {
            if ($user->fill($input)->save()) {
                flash('更新用户信息成功！', 'success');
                return true;
            }
            flash('更新用户信息失败！', 'error');
            return false;
        }
        abort(404);
    }

    /**
     * 修改个人中心用户信息
     * @param $request
     * @param $id
     * @return array
     * @author fangweibo
     */
    public function updateUserInfo($request, $id)
    {
        $user = $this->model->find($id);
        $input = $request->all();
        //return ['success' => true, 'code' => 0, 'msg' => '修改成功', 'data' => '', 'status' => 200];

        if ($request->file('headimgurl')) {
            $res = upload_file($request->file('headimgurl'), 'headimgurl', 'image');
            if (!$res['status']) { //上传失败
                return ['success' => false, 'code' => 422, 'msg' => $res['error'], 'data' => '', 'status' => 422];
            }
            $input['headimgurl'] = $res['path']; //上传成功图片路径
        }

        if ($user) {
            if ($user->fill($input)->save()) {
                return ['success' => true, 'code' => 0, 'msg' => '修改成功', 'data' => '', 'status' => 200];
            } else {
                return ['success' => false, 'code' => 422, 'msg' => '修改失败', 'data' => '', 'status' => 422];
            }
        }else{
            return ['success' => false, 'code' => 1002, 'msg' => '找不到用户', 'data' => '', 'status' => 200];
        }

    }

    /**
     * 修改密码
     * @param $request
     * @param $id
     * @return array
     * @author lf
     */
    public function updatePwd($request)
    {
        $input = $request->all();
        $data['password'] = bcrypt($input['password']);
        if (DB::table('users')->where('id', $input['id'])->update($data)) {
            return ['success' => true, 'code' => 200, 'msg' => '修改成功', 'data' => '', 'status' => 200];
        } else {
            return ['success' => false, 'code' => 422, 'msg' => '修改失败', 'data' => '', 'status' => 422];
        }
    }

    /**
     * 购物车列表
     * @Author wangyan
     */
    public function shopcar()
    {
        $userInfo = JWTAuth::toUser()->toArray();
        $list = DB::table('shopping_cart')
            ->select(
                'shopping_cart.id', 'shopping_cart.goods_id', 'shopping_cart.user_id', 'shopping_cart.goods_attr_id', 'shopping_cart.num', 'shopping_cart.attr_values', 'goods_attr.sellprice as price',
                'goods.thumb', 'goods.name', 'goods_attr.stock'
            )
            ->where(['shopping_cart.user_id' => $userInfo['id']])
            ->leftJoin('goods', 'shopping_cart.goods_id', '=', 'goods.id')
            ->leftJoin('goods_attr', 'shopping_cart.goods_attr_id', '=', 'goods_attr.id')
            ->orderBy('shopping_cart.created_at', 'desc')
            ->get();
        $arr = [];
        $arr['stock_no'] = [];
        $arr['stock_yes'] = [];
        foreach ($list as $k => $v) {
            $goods_attr = DB::table('goods_attr')->where(['id' => $v->goods_attr_id])->first();
            $list[$k]->check = 0;
            if (count($goods_attr) <= 0 || $goods_attr->stock < $v->num) {
                $arr['stock_no'][] = $list[$k];
            }else{
                $arr['stock_yes'][] = $list[$k];
            }
        }
        if ($arr) {
            return $arr;
        } else {
            return false;
        }
    }

    /**
     * 删除购物车
     * @param $id
     * @Author wangyan
     */
    public function destroyCarById($id){
        $res = DB::table('shopping_cart')->where(['id' => $id])->delete();
        if($res){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 获取所有用户代理列表信息
     * @return \Illuminate\Support\Collection
     * @author fangweibo
     */
    public function getAllUserAgency($request)
    {
        $input = $request->all();
        if (!empty($input['name'])) $where['name'] = $input['name'];
        if (!empty($input['phone'])) $where['phone'] = $input['phone'];
        if (!empty($input['id'])) $where['id'] = $input['id'];
        $list = \DB::table('users')
            ->where('daili','>','1')
            ->select('id as user_id','nickname','phone','agent_credit','daili')
            ->get();
        if($list){
            foreach($list as $k=>$v){
                $agency = \DB::table('agency')
                    ->where('user_id','=',$v->user_id)
                    ->get();
                if($agency){
                    $area = [];
                    foreach($agency as $key=>$val){
                        $list[$k]->created_at = $val->created_at;
                        $area[$key]['province'] = get_city_name($val->province);
                        $area[$key]['city'] = get_city_name($val->city);
                        $area[$key]['area'] = get_city_name($val->area);
                    }
                    $list[$k]->area = $area;
                }
                //代理团队人数
                $userInfo = \DB::table('users')->select('id','pid')->get();
                $tree = getChildsList($userInfo,$v->user_id);
                $list[$k]->count =count($tree,1)/4;
            }
        }
        return $list;
    }

    /**
     * 获取用户区域信息
     * @param $id 用户ID
     * @return \Illuminate\Support\Collection
     * @author fangweibo
     */
    public function getUserAgency($id)
    {
        $list = \DB::table('agency')->where('user_id',$id)->get();
        foreach($list as $k=>$v){
            $list[$k]->province = get_city_name($v->province);
            $list[$k]->city = get_city_name($v->city);
            $list[$k]->area = get_city_name($v->area);
        }
        return $list;
    }

}