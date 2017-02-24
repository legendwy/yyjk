<?php
namespace App\Repositories\Eloquent;
use App\Models\Image;

class ImageRepository extends Repository {
    public function model()
    {
        return Image::class;
    }

    /**
     * 图片列表
     * @return mixed
     * @Author wangyan
     */
    public function getImageList(){
        $image = $this->model->get()->toArray();
        return $image;
    }

    /**
     * 通过id获取图片
     * @param $id
     * @return mixed|static
     * @Author wangyan
     */
    public function getImageById($id){
        $image = $this->model->find($id)->toArray();
        return $image;
    }

    /**
     * 修改
     * @param $request
     * @param $id
     * @return bool
     * @Author wangyan
     */
    public function updateImageById($request, $id){
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
     * 通过广告位id获取广告
     * @param $id
     * @return mixed
     * @Author wangyan
     */
    public function getAdByPositionId($id){
        $list = $this->model
            ->where(['position_id' => $id])
            ->orderBy('sort', 'asc')
            ->get();
        return $list;
    }

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
     * 获取文章列表
     * @return mixed
     * @Author wangyan
     */
    public function getArticleList(){
        $list = $this->model
            ->select('article.*','article_category.name as type_name')
            ->leftJoin('article_category','article_category.id','=','article.type_id')
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