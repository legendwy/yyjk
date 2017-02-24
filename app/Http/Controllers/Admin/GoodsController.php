<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Repositories\Eloquent\GoodsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Eloquent\CategoryRepository;

class GoodsController extends Controller
{
    protected $category;
    protected $goods;
    protected $api_base;

    public function __construct(CategoryRepository $category, GoodsRepository $goods)
    {
        $this->api_base = new BaseController();
        $this->category = $category;
        $this->goods = $goods;
    }

    /**
     * 商品列表
     * @param Request $request
     * @return $this
     * @author: simayubo
     */
    public function index(Request $request)
    {
        $list = $this->goods->getAdminGoodsList($request);
        return view('admin.goods.list')->with(compact('list'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $attr_models = $this->goods->getAttrModels();
        $category = $this->category->getCategories();

        return view('admin.goods.create')->with(compact('category', 'attr_models'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $attr = $request->only('attr');
        $data = $request->all();
        if (!empty($attr['attr'])){
            foreach ($attr['attr'] as $key => $item) {
                if ($item['chengben_price'] == '' || $item['market_price'] == '' || $item['sellprice'] == '' || $item['stock'] == ''){
                    unset($attr['attr'][$key]);
                }
            }
            if (empty($attr['attr'])) unset($data['attr']);
        }
        $rule =  [
            'name'          =>  'required',
            'category_id'   =>  'required',
            'thumb_hidden'         =>  'required',
            'content'       =>  'required',
            'pic'           =>  'required',
            'attr'           =>  'required'
        ];
        $message = [
            'name.required'          =>  '商品名称不能为空！',
//            'name.unique'           =>  '该商品已存在！',
            'category_id.required'   =>  '商品分类不能为空！',
            'thumb_hidden.required'         =>  '商品缩略图不能为空！',
            'content.required'       =>  '商品详情不能为空！',
            'pic.required'           =>  '商品图册不能为空！',
            'attr.required'          =>  '商品属性不能为空！'
        ];
        if ($data['xian'] == 1){
            $rule['date_star'] = 'required';
            $rule['date_end'] = 'required';

            $message['date_star.required'] = '限时开始时间不能为空';
            $message['date_end.required'] = '限时结束时间不能为空';
        }
        $validate = \Validator::make($data,$rule, $message);
        if ($validate->fails()) {
            return $this->api_base->response->array($validate->errors())->setStatusCode(422);
        }
        if ($request->ajax()){
            return ['status' => 'success'];
        }
        $this->goods->addGoods($request);
//        dd();
        return redirect('admin/goods');
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
        $attr_models = $this->goods->getAttrModels();
        $category = $this->category->getCategories();
        $goods_info = $this->goods->find($id)->toArray();
        $imgs = [];
        if (!empty($goods_info['pic'])) {
            $imgs = explode(',', trim($goods_info['pic'], ','));
        }

        $attr = $this->goods->getAdminGoodsAttr($id);

        return view('admin.goods.edit')->with(compact('attr_models', 'category', 'goods_info', 'imgs', 'attr'));
    }

    /**
     * 删除商品属性
     * @param $id
     * @return array
     * @author: simayubo
     */
    public function deleteGoodsAttr($id, $goods_id){
        $rows = \DB::table('goods_attr')->where('goods_id', $goods_id)->count();
        if ($rows <= 1){
            return ['status' => 'fail', 'msg' => '商品至少保留一个属性'];
        }
        $res = $this->goods->deleteGoodsAttr($id);
        if ($res){
            return ['status' => 'success'];
        }else{
            return ['status' => 'fail', 'msg' => '删除失败'];
        }
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
        $attr = $request->only('attr');
        $data = $request->all();
        if (!empty($attr['attr'])){
            foreach ($attr['attr'] as $key => $item) {
                if ($item['chengben_price'] == '' || $item['market_price'] == '' || $item['sellprice'] == '' || $item['stock'] == ''){
                    unset($attr['attr'][$key]);
                }
            }
            if (empty($attr['attr'])) unset($data['attr']);
        }

        $rule = [
            'name'          =>  'required',
            'category_id'   =>  'required',
            'content'       =>  'required',
            'pic'           =>  'required',
        ];
        $message = [
            'name.required'          =>  '商品名称不能为空！',
//            'name.unique'           =>  '该商品已存在！',
            'category_id.required'   =>  '商品分类不能为空！',
            'content.required'       =>  '商品详情不能为空！',
            'pic.required'           =>  '商品图册不能为空！',
        ];
        if ($data['xian'] == 1){
            $rule['date_star'] = 'required';
            $rule['date_end'] = 'required';

            $message['date_star.required'] = '限时开始时间不能为空';
            $message['date_end.required'] = '限时结束时间不能为空';
        }
        $validate = \Validator::make($data, $rule, $message);
        if ($validate->fails()) {
            return $this->api_base->response->array($validate->errors())->setStatusCode(422);
        }
        if ($request->ajax()){
            return ['status' => 'success'];
        }
        $this->goods->updateGoods($request, $data, $id);

        return redirect('admin/goods');
    }

    /**
     * 商品排序
     * @param Request $request
     * @return array
     * @author: simayubo
     */
    public function setGoodsSort(Request $request){
        $data = $request->all();
        if (empty($data['goods_id']) || empty($data['sort'])){
            return ['status' => 'fail', 'msg' => '缺少参数'];
        }
        $res = $this->goods->setGoodsSort($request);
        if ($res){
            return ['status' => 'success', 'msg' => 'success'];
        }else{
            return ['status' => 'fail', 'msg' => '排序失败'];
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
        $resault = $this->goods->setGoodsStatus($id, -2);
        if ($resault){
            return ['status' => 'success'];
        }else{
            return ['status' => 'fail', 'msg' => '删除失败'];
        }
    }
    /**
     * 上传商品图片
     * @param Request $request
     * @return bool
     * @author: simayubo
     */
    public function uploadGoodsImages(Request $request){
        if ($request->file('file')){
            $res = upload_file($request->file('file'), 'goods', 'image');
            if (!$res['status']){
                return response()->json(['status' => 'fail', 'msg' => $res['error']])->setStatusCode(422);
            }
            return response()->json(['status' => 'success', 'msg' => 'success', 'path' => $res['path']]);

        }else{
            return response()->json(['status' => 'fail', 'msg' => '未上传文件'])->setStatusCode(422);
        }
    }

    /**
     * 删除商品图片
     * @param Request $request
     * @return array
     * @author: simayubo
     */
    public function deleteGoodsImage(Request $request){
        $path = $request->input('path');
        $good_id = $request->input('goods_id');
        if (empty($path)){
            return ['status' => 'fail', 'msg' => '错误：路径为空'];
        }
        $resault = $this->goods->deleteGoodsImage($path, $good_id);
        if ($resault){
            return ['status' => 'success', 'msg' => '删除成功', 'pic' => $resault];
        }else{
            return ['status' => 'success', 'msg' => '删除成功', 'pic' => ''];
        }

    }

    /**
     * 商品上下架
     * @param Request $request
     * @return array
     * @author: simayubo
     */
    public function goodsTopOrDown(Request $request){
        if (!$request->user('admin')->can('goods.top')) {
            return ['status' => 'fail', 'msg' => '权限不足 '];
        }
        $goods_id = $request->get('id');
        $status = $request->get('status');
        if (!$goods_id || !$status) {
            return ['status' => 'fail', 'msg' => '系统异常'];
        }
        $resault = $this->goods->setGoodsStatus($goods_id, $status == 1? -1 : 1);
        if ($resault){
            return ['status' => 'success'];
        }else{
            return ['status' => 'fail', 'msg' => '操作失败'];
        }
    }

    /**
     * 商品评论页面
     * @Author wangyan
     */
    public function goodsCommit($id){
        $commit = \DB::table('commit')->where(['goods_id'=>$id])->paginate(10);
        foreach ($commit as $key => $val){
            if($val->pic){
                $commit[$key]->pic = explode('@', trim($val->pic,'@'));
            }
        }
//        dd($commit);
        return view('admin.goods.commit')->with(compact('commit'));
    }

    /**
     * 商品评论删除
     * @Author wangyan
     */
    public function deleteGoodsCommit(Request $request){
        $id = $request->get('id');
        $goods_id = \DB::table('commit')->where(['id'=>$id])->value('goods_id');
        $result = \DB::table('commit')->where(['id'=>$id])->delete();
        if ($result){
            \DB::table('goods')->where(['id' => $goods_id])->decrement('count_comment',1);
            return ['status' => 'success'];
        }else{
            return ['status' => 'fail', 'msg' => '操作失败'];
        }

    }

}
