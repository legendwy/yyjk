<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShippingAddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'area'=>'required',
            'street'=>'required',
            'address'=>'required',
            'name'=>'required',
            'phone'=>'required',
            'postcode'=>'required',
            'user_id'=>'required',
        ];
    }

    public function messages()
    {
        return [
            'area.required'=>'所在地区不能为空',
            'street.required'=>'街道信息不能为空',
            'address.required'=>'详细地址不能为空',
            'name.required'=>'收货人姓名不能为空',
            'phone.required'=>'手机号码不能为空',
            'postcode.required'=>'邮编不能为空',
            'user_id.required'=>'用户ID不能为空',
        ];
    }
}
