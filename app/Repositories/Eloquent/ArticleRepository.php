<?php
namespace App\Repositories\Eloquent;
use App\Models\Article;

class ArticleRepository extends Repository {
    public function model()
    {
        return Article::class;
    }

    /**
     * 获取文章列表
     * @return mixed
     * @Author wangyan
     */
    public function getArticleList(){
        $list = $this->model
            ->select('article.*','article_category.name as type_name')
            ->leftJoin('article_category','article_category.id','=','article.type_id')			->orderBy('article.type_id', 'asc')
            ->orderBy('article.sort', 'asc')
            ->paginate(10);
        return $list;
    }

    /**
     * 添加文章
     * @param $request
     * @return bool
     * @Author wangyan
     */
    public function addArticle($request){
        $input = $request->all();
        $id = $this->model->create($input);
        if ($id){
            flash('文章添加成功！', 'success');
            return true;
        }else{
            flash('文章添加失败！', 'error');
            return false;
        }
    }

    /**
     * 获取文章详情
     * @param $id
     * @return mixed
     * @Author wangyan
     */
    public function getArticleInfo($id){
        return $this->model->find($id);
    }

    /**
     * 修改文章
     * @param $request
     * @param $id
     * @return bool
     * @Author wangyan
     */
    public function updateArticle($request, $id){
        $article = $this->model->find($id);
        $input = $request->all();
        if($article){
            if($article->fill($input)->save()){
                flash('文章修改成功！', 'success');
                return true;
            }
        }
        flash('文章修改失败！', 'error');
        return false;
    }

    /**
     * 文章删除
     * @param $id
     * @return bool
     * @Author wangyan
     */
    public function destroyArticle($id){
        $article = $this->model->find($id);
        if($article){
            $isDelete = $this->model->destroy($id);
            if ($isDelete){
                flash('文章删除成功！', 'success');
                return true;
            }
            flash('文章删除失败！', 'error');
            return false;
        }

    }


}