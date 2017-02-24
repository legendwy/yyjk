<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleRequest extends FormRequest
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
            'title'   => 'required',
            'type_id' => 'required',
            'sort'    => 'required',
            'desc'    => 'required',
            'content' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'title.required'    =>  '文章标题不能为空',
            'type_id.required'  =>  '文章栏目必须选择',
            'sort.required'     =>  '排序不能为空',
            'desc.required'     =>  '文章摘要不能为空',
            'content.required'  =>  '文章内容不能为空',
        ];
    }
}
