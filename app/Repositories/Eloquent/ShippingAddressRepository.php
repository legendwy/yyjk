<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/15
 * Time: 16:52
 */

namespace App\Repositories\Eloquent;


use App\Models\ShippingAddress;

class ShippingAddressRepository extends Repository
{
    public function model()
    {
        return ShippingAddress::class;
    }

    /**
     * 更新收货地址
     * @param $request
     * @param $id
     * @return array
     * @author fangweibo
     */
    public function updateAddress($request,$id)
    {
        $address = $this->model->find($id);
        if($address){
            if($address->fill($request->all())->save()){
                return true;
            }else{
                return false;
            }
        }
    }

    /**
     * 验证接受的数据
     * @param $request
     * @return array
     * @author fangweibo
     */
    public function validateData($request,$id='')
    {
        if($request->status ==1){ //将该地址设置为默认地址
            //判断该用户是否设置过默认地址
            $where['status'] = 1;
            $where['user_id'] = $request->user_id;
            $default_addr = \DB::table('shipping_address')->where($where)->first();
            //如果设置过默认地址,将原默认地址变成普通地址
            if($default_addr){
                $res1 = \DB::table('shipping_address')->where('id',$default_addr->id)->update(['status'=>0]);
                if(!$res1){
                    return ['success'=>false,'code'=>422,'msg'=>'原默认地址取消失败','data'=>[''],'status'=>422];
                }
            }
        }

        $validate = \Validator::make($request->all(),[
            'address'=>'required',
            'name'=>'required',
            'phone'=>array('required','numeric','regex:/^1[34578][0-9]{9}$/'),
//            'phone'=>'required|numeric|regex:/^1[34578][0-9]{9}$/',
        ],[
            'address.required'=>'详细地址不能为空',
            'name.required'=>'收货人姓名不能为空',
            'phone.required'=>'手机号码不能为空',
            'phone.numeric'=>'手机号必须是数字',
            'phone.regex'=>'手机格式不正确',
        ]);
        if($validate->fails()){
            return ['success'=>false,'code'=>422,'msg'=>'数据验证失败','data'=>['error'=>$validate->errors()],'status'=>422];
        }
		
		if($request->postcode){
            $validatee = \Validator::make($request->all(),[
                'postcode' =>array('numeric','regex:/^[0-9]{6}$/')
            ],[
                'postcode.numeric'=>'邮编必须是数字',
                'postcode.regex'=>'邮编格式不正确'
            ]);
            if($validatee->fails()){
                return ['success'=>false,'code'=>422,'msg'=>'数据验证失败','data'=>['error'=>$validatee->errors()],'status'=>422];
            }
        }

        if(!$id){
            $validater = \Validator::make($request->all(),[
                'area'=>'required',
            ],[
                'area.required'=>'所在地区不能为空',
            ]);
            if($validater->fails()){
                return ['success'=>false,'code'=>422,'msg'=>'数据验证失败','data'=>['error'=>$validater->errors()],'status'=>422];
            }
        }

    }

    /**
     *
     * @param $str
     * @Author wangyan
     */
    public function getNameById($str){
        $arr = explode(',', trim($str, ','));
        $array = [];
        foreach ($arr as $item){
            $array[] = get_city_name($item);
        }
        return $array;
    }







}