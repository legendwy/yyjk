<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LogisticsRequest extends FormRequest
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
        $rules = [
            'bm' => 'required',
            'phone' => 'required'
        ];
        if (request('id','')) {
            $rules['name'] = 'required|unique:logistics,name,'.$this->id;
        }else{
            $rules['name'] = 'required|unique:logistics,name';
        }
        return $rules;

    }
    public function messages()
    {
        return [
            'name.required' => '物流公司名称不能为空',
            'name.unique' => '已存在该物流公司名称',
            'bm.required' => '物流公司编码不能为空',
            'phone.required' => '物流公司电话不能为空'
        ];
    }
}
