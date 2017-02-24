<?php

namespace App\Http\Controllers\Api\v1_0;

use App\Http\Controllers\Api\BaseController;
use App\Models\Address;
use App\Models\ShippingAddress;
use App\Repositories\Eloquent\ShippingAddressRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;

class ShippingAddressController extends BaseController
{
    protected $shippingAddress;

    public function __construct(ShippingAddressRepository $shippingAddress)
    {
        $this->shippingAddress = $shippingAddress;
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
     * Store a newly created resource in storage.
     *新增收货地址
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @author fangweibo
     */
    public function store(Request $request)
    {
        $user_info = JWTAuth::toUser();
        $request['user_id'] = $user_info->id;
        //验证接收的数据
        $result = $this->shippingAddress->validateData($request);
        if($result){
            return $this->returnMsg($result['success'],$result['code'],$result['msg'],$result['data'],$result['status']);
        }

        $data = \DB::table('shipping_address')->where('user_id',$user_info->id)->get()->toArray();
        if(empty($data)&&$request->status==0){ //判断用户是否没有收货地址
            $request['status'] = 1;
        }

        $area = explode(',',$request->area);
        $area = array_filter($area);
        $request['province'] = $area[0];
        $request['city'] = $area[1];
        $request['area'] = $area[2];


        $res = $this->shippingAddress->create($request->all());
        if($res){
            return $this->returnMsg(true,0,'添加成功');
        }else{
            return $this->returnMsg(false,422,'添加失败',[],422);
        }
    }

    /**
     * Display the specified resource.
     * 收货地址信息
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * @author fangweibo
     */
    public function show($id)
    {
        JWTAuth::toUser();
        $address = ShippingAddress::findOrFail($id);
        if($address['province']){
            $address['province'] = get_city_name($address['province']); //省
        }
        if($address['city']){
            $address['city'] = get_city_name($address['city']); //市
        }
        if($address['area']){
            $address['area'] = get_city_name($address['area']); //区
        }

        return $this->returnMsg(true,0,'success',$address->toArray());
    }

    /**
     * Show the form for editing the specified resource.
     *获取
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *修改收货地址
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * @author fangweibo
     */
    public function update(Request $request,$id)
    {
        $user_info = JWTAuth::toUser();
        $request['user_id'] = $user_info->id;
//        return $request;
        //验证接收的数据
        $result = $this->shippingAddress->validateData($request,$id);
        if($result){
            return $this->returnMsg($result['success'],$result['code'],$result['msg'],$result['data'],$result['status']);
        }

        if($request->area){
            $area = explode(',',$request->area);
            $area = array_filter($area);
            $request['province'] = $area[0];
            $request['city'] = $area[1];
            $request['area'] = $area[2];
        }else{
            unset($request['area']);
        }

        $res = $this->shippingAddress->updateAddress($request,$id);
        if($res){
            return $this->returnMsg(true,0,'修改成功');
        }else{
            return $this->returnMsg(false,422,'修改失败',[],422);
        }
    }

    /**
     * Remove the specified resource from storage.
     * 删除收货地址
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * @author fangweibo
     */
    public function destroy($id)
    {
        JWTAuth::toUser();
        $res = $this->shippingAddress->delete($id);
        if($res){
            return $this->returnMsg(true,0,'删除成功');
        }else{
            return $this->returnMsg(false,422,'删除失败',[],422);
        }
    }

    public function getNameById(Request $request){
        $str = $request->get('str');
        if(empty($str)){
            return $this->returnMsg(false, 400, '缺失参数（省市区id字符串）');
        }
        $result = $this->shippingAddress->getNameById($str);
        if($result){
            return $this->returnMsg(true, 0, 'success', $result);
        }else{
            return $this->returnMsg(false, 1000, '获取失败' );
        }
    }
}
