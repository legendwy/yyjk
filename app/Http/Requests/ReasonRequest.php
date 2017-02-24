<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReasonRequest extends FormRequest
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
        if (request('id','')) {
            $rules['title'] = 'required|unique:reason,title,'.$this->id;
        }else{
            $rules['title'] = 'required|unique:reason,title';
        }
        return $rules;
    }

    public function messages()
    {
        return [
            'title.required' => '退款退货理由不能为空',
            'title.unique' => '已存在该退款退货理由'
        ];
    }
}
