<?php
namespace App\Repositories\Eloquent;
use App\Models\ArticleCategory;

class ArticleCategoryRepository extends Repository {
    public function model()
    {
        return ArticleCategory::class;
    }

    /**
     * 获取文章分类列表
     * @return mixed
     * @Author wangyan
     */
    public function getCategoryList(){
        $list = $this->model->get();
        return $list;
    }

    /**
     * 添加分类
     * @param $request
     * @return bool
     * @Author wangyan
     */
    public function addCategory($request){
        $input = $request->all();
        $data = [];
        $data['name'] = $input['name'];
        $id = $this->model->create($data);
        if ($id){
            flash('分类添加成功！', 'success');
            return true;
        }else{
            flash('分类添加失败！', 'error');
            return false;
        }
    }

    /**
     * 获取详情
     * @param $id
     * @return mixed
     * @Author wangyan
     */
    public function getCategoryInfo($id){
        return $this->model->find($id)->toArray();
    }

    /**
     * 修改分类
     * @param $request
     * @param $id
     * @return bool
     * @Author wangyan
     */
    public function updateCategory($request, $id){
        $category = $this->model->find($id);
        $input = $request->all();
        if($category){
            if($category->fill($input)->save()){
                flash('修改分类成功！', 'success');
                return true;
            }
        }
        flash('修改分类失败！', 'error');
        return false;
    }


}