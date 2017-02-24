<?php

namespace App\Repositories\Eloquent;
use App\Models\Reason;

/**
 * 退款退货理由仓库
 * Class LogisticsRepository
 * @package App\Repositories\Eloquent
 */
class ReasonRepository extends Repository {

    public function model()
    {
        return Reason::class;
    }

    /**
     * 获取列表
     * @param $request
     * @return mixed
     * @author: simayubo
     */
    public function getList($request){
        $input = $request->all();
        $where = [];
        if (!empty($input['name'])) $where['name'] = $input['name'];
        if (!empty($input['description'])) $where['description'] = $input['description'];

        $list = $this->model->where($where)->paginate(20);
        return $list;
    }

    /**
     * 更新角
     * @param $request
     * @return bool
     * @author: simayubo
     */
    public function updateReason($request, $id){
        $logistics = $this->model->find($id);
        if ($logistics) {
            if ($logistics->fill($request->all())->save()) {
                flash('修改成功！', 'success');
                return true;
            }
            flash('修改失败！', 'error');
            return false;
        }
        abort(404);
    }


    /**
     * 删除
     * @param $id
     * @return bool
     * @author: simayubo
     */
    public function destroy($id){
        $is_delete = Reason::whereId($id)->delete();
        if ($is_delete) {
            flash('删除成功！', 'success');
            return true;
        }
        flash('删除失败！', 'error');
        return false;
    }
}