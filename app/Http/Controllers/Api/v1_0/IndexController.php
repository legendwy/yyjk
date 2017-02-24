<?php

namespace App\Http\Controllers\Api\v1_0;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use App\Repositories\Eloquent\GoodsRepository;
use App\Repositories\Eloquent\AdRepository;

class IndexController extends BaseController
{
    protected $goods;
    protected $ad;
    public function __construct(
        GoodsRepository $goods,
        AdRepository $ad
    )
    {
        $this->goods = $goods;
        $this->ad = $ad;
    }

    /**
     * 首页轮播图
     * @return mixed
     * @Author wangyan
     */
    public function banner(){
        $list = $this->ad->getAdByPositionId(1)->toArray();
        if($list){
            return $this->returnMsg(true, 0, 'success', $list);
        }else{
            return $this->returnMsg(false, 1001, '暂无轮播图');
        }
    }


    /**
     * 首页商品列表
     * @param Request $request
     * @return mixed
     * @Author wangyan
     */
    public function goodsList(Request $request){
        $list = $this->goods->getGoodsListBySts($request);
        if($list){
            return $this->returnMsg(true, 0, 'success', $list);
        }else{
            return $this->returnMsg(false, 1001, '暂无商品');
        }
    }

    /**
     * 搜索
     * @param Request $request
     * @return mixed
     * @Author wangyan
     */
    public function search(Request $request){
        $keyword = $request->get('key');
        if(empty($keyword)){
            return $this->returnMsg('false', 1002, '请输入关键字');
        }
        $list = $this->goods->getGoodsListByKey($request);
        if($list){
            return $this->returnMsg(true, 0, 'success', $list);
        }else{
            return $this->returnMsg(false, 1001, '暂无商品');
        }
    }



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
