<?php
namespace App\Repositories\Eloquent;
use App\Models\Sys;

class SysRepository extends Repository {
    public function model()
    {
        return Sys::class;
    }

    /**
     * 获取配置列表
     * @return array
     * @Author wangyan
     */
    public function getSysList(){
        $config = $this->model->orderBy('sort', 'asc')->get()->toArray();
        $sort_config = array();
        foreach ($config as $key => $value) {
            $sort_config[$value['id']] = $value;
        }
        return $sort_config;
    }

    public function updateSys($request){
        $input = $request->all();
        if($request->file('4')){
            $res = upload_file($request->file('4'), 'sys', 'image');
            if (!$res['status']){
                flash($res['error'], 'error');
                return false;
            }
            $input['4'] = $res['path'];
        }
        if($request->file('20')){
            $res = upload_file($request->file('20'), 'sys', 'image');
            if (!$res['status']){
                flash($res['error'], 'error');
                return false;
            }
            $input['20'] = $res['path'];
        }
        $data = [];
        unset($input['_token']);
        foreach ($input as $k => $v){
            $data[] = array(
                'key' => $k,
                'value' => $v
            );
        }
        foreach ($data as $key => $val){
            $this->model
                ->where(['id' => $val['key']])
                ->update(['value' => $val['value']]);
        }
        flash('配置修改成功！', 'success');
    }



}