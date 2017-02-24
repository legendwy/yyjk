<?php
namespace App\Repositories\Eloquent;
use App\Models\Category;
use Illuminate\Http\Request;
use Cache;

class CategoryRepository extends Repository {

    public function model()
    {
        return Category::class;
    }

    /**
     * 根据parent_id查询分类列表
     * @param int $parent_id
     * @return mixed
     * @author: simayubo
     */
    public function getCategoryList($parent_id = 0){
        $list = $this->model->where([
            'parent_id'     =>  $parent_id,
        ])->orderBy('sort', 'desc')->get()->toArray();

        return $list;
    }

    /**
     * 添加商品分类
     * @param $request
     * @return bool
     * @author: simayubo
     */
    public function addCategory($request){
        $input = $request->all();
        $data = $input;
        if ($request->file('icon')){
            $res = upload_file($request->file('icon'), 'category', 'image');
            if (!$res['status']){
                flash($res['error'], 'error');
                return false;
            }
            $data['icon'] = $res['path'];
        }

        $id = $this->model->create($data);
        if ($id){
            if ($request->file('icon')) {
                \DB::table('files')->where('path', $data['icon'])->update(['status' => 1]);
            }
            flash('分类添加成功！', 'success');
        }else{
            flash('分类添加失败！', 'error');
        }

    }


    /**
     * 获取所有分类并写入缓存
     * @return mixed
     * @author: simayubo
     */
    public function getCategorySetCache(){
        $list = $this->model->where(['parent_id' => 0])->orderBy('sort', 'desc')->get()->toArray();
        foreach ($list as $key => $item) {
            $_list = $this->model->where(['parent_id' => $item['id']])->get()->toArray();
            foreach ($_list as $_key => $_item){
                $_list[$_key]['child'] = $this->model->where(['parent_id' => $_item['id']])->orderBy('sort', 'desc')->get()->toArray();
            }
            $list[$key]['child'] = $_list;
        }
        Cache::forever('categoryList', $list);
        return $list;
    }
    /**
     * 更新分类数据
     * @param $request
     * @return bool
     * @author: simayubo
     */
    public function updateCategory($request){
        $category = $this->model->find($request->id);
        $data = $request->all();

        if ($request->file('icon')){
            $res = upload_file($request->file('icon'), 'category', 'image');
            if (!$res['status']){
                flash($res['error'], 'error');
                return false;
            }
            $data['icon'] = $res['path'];
        }
//        dd($request->file('icon'));
        if ($category){
            $isUpdate = $category->update($data);
            if ($isUpdate){
                $this->getCategorySetCache();
                if ($request->file('icon')) {
                    \DB::table('files')->where('path', $data['icon'])->update(['status' => 1]);
                }
                flash('修改分类成功！', 'success');
                return true;
            }
            flash('修改分类失败！', 'error');
            return false;
        }
        abort('404', '找不到分类数据');
    }

    /**
     * 获取所有分类
     * @author: simayubo
     */
    public function getCategories(){
        $category_list = Cache::get('categoryList');
        if (!$category_list){
            $category_list = $this->getCategorySetCache();
        }
        return $category_list;
    }

    /**
     * 删除分类
     * @param $id
     * @return bool
     */
    public function destoryCategory($id){
        $list = $this->model->where('parent_id', $id)->first();
        if ($list){
            return -2;
        }
        $isDelete = $this->model->destroy($id);
        if ($isDelete){
            $this->getCategorySetCache(); //更新缓存
            return 1;
        }
        return -1;
    }

}