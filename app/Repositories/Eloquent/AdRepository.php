<?php
namespace App\Repositories\Eloquent;
use App\Models\Ad;

class AdRepository extends Repository {
    public function model()
    {
        return Ad::class;
    }



    /**
     * 通过广告位id获取广告
     * @param $id
     * @return mixed
     * @Author wangyan
     */
    public function getAdByPositionId($id){
        $list = $this->model
            ->select('ad.*','ad_position.name','ad_position.size')
            ->leftJoin('ad_position','ad_position.id','=','ad.position_id')
            ->where(['ad.position_id' => $id])
            ->orderBy('sort', 'asc')
            ->get();
        if($list){
            return $list;
        }else{
            return false;
        }

    }

    /**
     * 添加图片
     * @param $request
     * @return bool
     * @Author wangyan
     */
    public function addAd($request){
        $input = $request->all();
        $data = $input;
        if($request->file('image')){
            $res = upload_file($request->file('image'), 'ad', 'image');
            if (!$res['status']){
                flash($res['error'], 'error');
                return false;
            }
            $data['image'] = $res['path'];
        }
        $id = $this->model->create($data);
        if ($id){
            if ($request->file('image')) {
                \DB::table('files')->where('path', $data['image'])->update(['status' => 1]);
            }
            flash('图片添加成功！', 'success');
        }else{
            flash('图片添加失败！', 'error');
        }
    }

    /**
     * 通过id获取图片
     * @param $id
     * @return mixed
     * @Author wangyan
     */
    public function getAdById($id){
        $ad = $this->model->find($id)->toArray();
        return $ad;
    }

    /**
     * 修改图片
     * @param $request
     * @param $id
     * @return bool
     * @Author wangyan
     */
    public function updateAdById($request,$id){
        $image = $this->model->find($id);
        $input = $request->all();
        $data = $input;
        if($image){
            if($request->file('image')){
                $res = upload_file($request->file('image'), 'ad', 'image');
                if (!$res['status']){
                    flash($res['error'], 'error');
                    return false;
                }
                $data['image'] = $res['path'];
            }
            if($image->fill($data)->save()){
                flash('图片修改成功！', 'success');
                return true;
            }
        }
        flash('图片修改失败！', 'error');
        return false;
    }

    /**
     * 删除轮播图
     * @param $id
     * @return bool
     * @Author wangyan
     */
    public function destroyAdById($id){
        $ad = $this->model->find($id);
        if($ad){
            $isDelete = $this->model->destroy($id);
            if ($isDelete){
                flash('轮播图删除成功！', 'success');
                return true;
            }
            flash('轮播图删除失败！', 'error');
            return false;
        }

    }



}