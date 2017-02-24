<?php

namespace App\Http\Controllers\Admin;

use App\Models\AttrModels;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class AttrModelsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $list = AttrModels::orderBy('id', 'desc')->paginate(20);
        return view('admin.attr_models.list')->with(compact('list'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.attr_models.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name'  =>  'required|unique:attr_models,name',
            'data'  =>  'required'
        ], [
            'name.required'   =>    '模型名称不能为空',
            'name.unique'   =>    '模型名称已存在',
            'data.required'   =>    '模型属性不能为空',
        ]);

        $models_name = $request->input('name');
        $status = 1;
        $model_array = [];
        DB::beginTransaction();
        foreach ($request->input('data') as $v) {
            if ($v['attr'] == '') continue;
            $attr_id = DB::table('goods_attr_ibute')->insertGetId(['name' => $v['attr'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
            if (!$attr_id) {
                $status = 0;
                break;
            }
            $attr_values_id_array = [];
            foreach ($v['attrValue'] as $_v) {
                if ($_v == '') continue;
                $attr_values_id = DB::table('goods_attr_value')->insertGetId([
                    'value'  =>  $_v,
                    'pid'   =>  $attr_id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
                if (!$attr_values_id) {
                    $status = 0;
                    break;
                }
                $attr_values_id_array[] = $attr_values_id;
            }
            $model_array[$attr_id] = $attr_values_id_array;
        }
        if ($model_array){
            $attr_models_id = DB::table('attr_models')->insertGetId(['name' => $models_name, 'attr' => serialize($model_array)]);
            if (!$attr_models_id) $status = 0;
        }else{
            $status = 0;
        }
        if ($status == 1){
            DB::commit();
            return ['status' => 'success', 'msg' => 'success', 'last_id' => $attr_models_id];
        }else{
            DB::rollback();
            return ['status' => 'fail', 'msg'   =>  '系统异常'];
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $attr_models = DB::table('attr_models')->find($id);
        $attr = unserialize($attr_models->attr);
        $attr_arr = [];
        foreach ($attr as $k => $v) {
            $_attr = DB::table('goods_attr_ibute')->find($k);
            $_attr_values = DB::table('goods_attr_value')->whereIn('id', $v)->get()->toArray();
            $attr_arr[] = [
                'attr'    =>    $_attr,
                'attr_values'   =>  $_attr_values
            ];
        }
//        dd($attr_arr);
        return view('admin.attr_models.edit')->with(compact('attr_arr', 'attr_models'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name'  =>  'required|unique:attr_models,name,'.$id
        ], [
            'name.required'   =>    '模型名称不能为空',
            'name.unique'   =>    '模型名称已存在'
        ]);

        $models_name = $request->input('name');

        DB::beginTransaction();
        $status = 1;
        $model_array = [];
        if (!empty($request->input('data'))){
            //插入新属性
            foreach ($request->input('data') as $v) {
                if ($v['attr'] == '') continue;
                $attr_id = DB::table('goods_attr_ibute')->insertGetId(['name' => $v['attr'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
                if (!$attr_id) {
                    $status = 0;
                    break;
                }
                $attr_values_id_array = [];
                foreach ($v['attrValue'] as $_v) {
                    if ($_v == '') continue;
                    $attr_values_id = DB::table('goods_attr_value')->insertGetId([
                        'value'  =>  $_v,
                        'pid'   =>  $attr_id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);
                    if (!$attr_values_id) {
                        $status = 0;
                        break;
                    }
                    $attr_values_id_array[] = $attr_values_id;
                }
                $model_array[$attr_id] = $attr_values_id_array;
            }
        }

        $update_attr = $request->input('data_update');
        //更新已有属性
        foreach ($update_attr['attr'] as $k => $v) {
            $res = DB::table('goods_attr_ibute')->where('id', $k)->update(['name' => $v]);
            if ($res < 0){
                $status = 0;
                break;
            }
        }
        //更新已有属性值
        foreach ($update_attr['attrValue'] as $k => $v) {
            $res = DB::table('goods_attr_value')->where('id', $k)->update(['value' => $v]);
            if ($res < 0){
                $status = 0;
                break;
            }
        }
        //查出模型原有属性
        $attr_models_info = DB::table('attr_models')->find($id);
        $_attr = unserialize($attr_models_info->attr);
        if (!empty($model_array)){
            foreach ($model_array as $k => $v) {
                $_attr[$k] = $v;
            }
        }

        if ($_attr){
            $attr_models_id = DB::table('attr_models')->where('id', $id)->update(['name' => $models_name, 'attr' => serialize($_attr)]);
            if ($attr_models_id < 0) $status = 0;
        }else{
            $status = 0;
        }
        if ($status == 1){
            DB::commit();
            return ['status' => 'success', 'msg' => 'success'];
        }else{
            DB::rollback();
            return ['status' => 'fail', 'msg'   =>  '系统异常'];
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $attr = DB::table('attr_models')->where('id', $id)->value('attr');
        $attr = unserialize($attr);
        $ids = [];
        foreach ($attr as $key => $value) {
            $ids[] = $key;
        }
        DB::beginTransaction();

        $res1 = DB::table('attr_models')->delete($id);
        $res2 = DB::table('goods_attr_ibute')->whereIn('id', $ids)->delete();
        if ($res1 && $res2){
            DB::commit();
            flash('删除成功', 'success');
        }else{
            DB::rollBack();
            flash('删除失败', 'error');
        }
        return redirect('admin/attr_models');
    }

    /**
     * 获取规格模型
     * @param $id
     * @return array
     * @author: simayubo
     */
    public function getAttrModels($id){
        $attr_models = DB::table('attr_models')->where('id', $id)->first();
        if (!$attr_models){
            return ['status' => 'fail', 'msg' => '模型不存在！'];
        }
        $attr_array = unserialize($attr_models->attr);
        $select = '<div id="form1">';
        foreach ($attr_array as $k => $v) {
            $attr = DB::table('goods_attr_ibute')->where('id', $k)->first();
            $attr_values = DB::table('goods_attr_value')->whereIn('id', $v)->get();

            $select .= '<div class="col-sm-2"><label class="col-sm-12" style="float: left">'.$attr->name.'</label><select class="col-sm-12 form-control" style="float:left; margin-right: 10px;" name="'.$attr->id.'">';
            foreach ($attr_values as $_k => $_v) {
                $select .= '<option value="'.$_v->id.'">'.$_v->value.'</option>';
            }
            $select .= '</select></div>';
        }
        $select .= '</div><button class="btn btn-outline btn-success" type="button" id="add_values">添加</button>';
        if ($select == ''){
            return ['status' => 'fail', 'msg' => '数据异常'];
        }else{
            return ['status' => 'success', 'msg' => 'success', 'select' => $select];
        }
    }

    /**
     * 获取属性值
     * @param Request $request
     * @return array
     * @author: simayubo
     */
    public function getAttrAndValues(Request $request, $num){
        $num = $num;
        $data = $request->all();
        $tr = '<tr class="tr-'.$num.'"><td>';
        if (!empty($data['type'])){
            $tr .= '无属性<input type="hidden" name="attr['.$num.'][attr][0]" value="0"><input type="hidden" name="attr['.$num.'][attr_values][0]" value="0"><span class="check-0"></span>';
        }else{
            $check_str = 'check-';
            $check_text_str = 'check-';
            foreach ($data as $k => $v) {
                $attr = DB::table('goods_attr_ibute')->where('id', $v['name'])->first();
                $str = '无属性,';
                $input = '<input type="hidden" name="attr['.$num.'][attr]['.$v['value'].']" value="0"><input type="hidden" name="attr['.$num.'][attr_values]['.$v['value'].']" value="0">';
                if ($v['value'] != 0){
                    $attr_values = DB::table('goods_attr_value')->where('id', $v['value'])->first();
                    if ($attr_values){
                        $str = $attr_values->value.'，';
                        $check_str .= $attr_values->id.'-';
                        $check_text_str .= $attr_values->value.'-';
                        $input = '<input type="hidden" name="attr['.$num.'][attr]['.$v['value'].']" value="'.$attr->id.'"><input type="hidden" name="attr['.$num.'][attr_values]['.$v['value'].']" value="'.$attr_values->id.'">';
                    }
                }
                $tr .= '<b style="color: #337ab7;">'.$attr->name.'：</b>'.$str.$input;
            }
            $tr .= '<span class="'.$check_str.' '.$check_text_str.'"></span>';
        }

        $tr .= '</td>';
        $tr .= '<td><input class="form-control goods_number" name="attr['.$num.'][goods_number]" style="width: 120px; border-color: #ccc"></td>';
        $tr .= '<td><input class="form-control chengben_price" name="attr['.$num.'][chengben_price]" style="width: 100px; border-color: #ccc"></td>';
        $tr .= '<td><input class="form-control market_price" name="attr['.$num.'][market_price]" style="width: 100px; border-color: #ccc"></td>';
        $tr .= '<td><input class="form-control sellprice" name="attr['.$num.'][sellprice]" style="width: 100px; border-color: #ccc"></td>';
        $tr .= '<td><input class="form-control stock" name="attr['.$num.'][stock]" style="width: 100px; border-color: #ccc"></td>';
        $tr .= '<td><button class="btn btn-sm btn-outline btn-danger del_values" onclick="del_values_js('.$num.')" aid="'.$num.'" title="移除" type="button"><span class="fa fa-times"></span></button></td>';
        $tr .= '</tr>';

        return ['tr' => $tr];
    }

    /**
     * 添加模型属性值
     * @param Request $request
     * @return array
     * @author: simayubo
     */
    public function addAttrValue(Request $request){

        DB::beginTransaction();
        $attr_value_id = DB::table('goods_attr_value')
            ->insertGetId([
                'value' => $request->input('attr_value'),
                'pid'   => $request->input('attr_id'),
                'created_at'    =>  Carbon::now(),
                'updated_at'    =>  Carbon::now()
            ]);
        if (!$attr_value_id){
            DB::rollBack();
            return ['status' => 'fail', 'msg' => '系统异常'];
        }
        $attr = DB::table('attr_models')->where('id', $request->input('attr_models_id'))->value('attr');
        if (!$attr){
            DB::rollBack();
            return ['status' => 'fail', 'msg' => '模型找不到'];
        }
        $attr_arr = unserialize($attr);
        foreach ($attr_arr as $k => $v) {
            if ($request->input('attr_id') == $k){
                $attr_arr[$k][] = $attr_value_id;
            }
        }
        $resault = DB::table('attr_models')->where('id', $request->input('attr_models_id'))->update(['attr' => serialize($attr_arr)]);
        if ($resault){
            DB::commit();
            return ['status' => 'success', 'id' => $attr_value_id];
        }else{
            DB::rollBack();
            return ['status' => 'fail', 'msg' => '系统异常'];
        }
    }
    /**
     * 删除模型属性
     * @param Request $request
     * @return array
     * @author: simayubo
     */
    public function delAttr(Request $request){

        DB::beginTransaction();
        $is_delete = DB::table('goods_attr_ibute')->delete($request->input('attr_id'));
        if (!$is_delete){
            DB::rollBack();
            return ['status' => 'fail', 'msg' => '系统异常'];
        }
        $attr = DB::table('attr_models')->where('id', $request->input('attr_models_id'))->value('attr');
        if (!$attr){
            DB::rollBack();
            return ['status' => 'fail', 'msg' => '模型找不到'];
        }
        $attr_arr = unserialize($attr);
        foreach ($attr_arr as $k => $v) {
            if ($k = $request->input('attr_id')){
                unset($attr_arr[$k]);
            }
        }
        if (empty($attr_arr)){
            DB::rollBack();
            return ['status' => 'fail', 'msg' => '不允许删除！模型至少要有一个属性！'];
        }
        $resault = DB::table('attr_models')->where('id', $request->input('attr_models_id'))->update(['attr' => serialize($attr_arr)]);
        if ($resault){
            DB::commit();
            return ['status' => 'success'];
        }else{
            DB::rollBack();
            return ['status' => 'fail', 'msg' => '系统异常'];
        }
    }
    /**
     * 删除模型属性值
     * @param Request $request
     * @return array
     * @author: simayubo
     */
    public function delAttrValue(Request $request){

        DB::beginTransaction();
        $is_delete = DB::table('goods_attr_value')->delete($request->input('attr_value_id'));
        if (!$is_delete){
            DB::rollBack();
            return ['status' => 'fail', 'msg' => '系统异常'];
        }
        $attr = DB::table('attr_models')->where('id', $request->input('attr_models_id'))->value('attr');
        if (!$attr){
            DB::rollBack();
            return ['status' => 'fail', 'msg' => '模型找不到'];
        }
        $attr_arr = unserialize($attr);
        $attr_count = count($attr_arr);
        $attr_value_count = 0;
        foreach ($attr_arr as $k => $v) {
            foreach ($v as $_k => $_v) {
                if ($request->input('attr_value_id') == $_v){
                    unset($attr_arr[$k][$_k]);
                }else{
                    $attr_value_count++;
                }
            }
        }
        if ($attr_count <= 1 && $attr_value_count <= 0){
            DB::rollBack();
            return ['status' => 'fail', 'msg' => '不允许删除！模型至少要有一个属性并且一个属性值！'];
        }
        $resault = DB::table('attr_models')->where('id', $request->input('attr_models_id'))->update(['attr' => serialize($attr_arr)]);
        if ($resault){
            DB::commit();
            return ['status' => 'success'];
        }else{
            DB::rollBack();
            return ['status' => 'fail', 'msg' => '系统异常'];
        }
    }
}
