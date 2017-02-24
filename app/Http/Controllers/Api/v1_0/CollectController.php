<?php

namespace App\Http\Controllers\Api\v1_0;

use App\Http\Controllers\Api\BaseController;
use App\Models\Collect;
use App\Repositories\Eloquent\CollectRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JWTAuth;

class CollectController extends BaseController
{
    protected $collect;

    public function __construct(CollectRepository $collect)
    {
        $this->collect = $collect;
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
        $id = explode(',',$id);
        $id = array_filter($id);
        $result = Collect::destroy($id);

        if($result){
            return $this->returnMsg(true,0,'删除成功');
        }else{
            return $this->returnMsg(false,422,'删除失败',[],422);

        }
    }

    /**
     * 获取用户的收藏列表
     * @param $id
     * @return mixed
     * @author:fangweibo
     */
    public function collectList(Request $request)
    {
        $userInfo = JWTAuth::toUser();
        $user_id = $userInfo['id'];
        $data = \DB::table('collect')
            ->where('collect.user_id','=',$user_id)
            ->leftjoin('goods','goods.id','=','collect.goods_id')
            ->select('collect.id','collect.goods_id','goods.thumb','goods.name','goods.sell_num')
            ->paginate(15);
        if($data){
            foreach($data as $k=>$v) {
                $sellprice = \DB::table('goods_attr')->where(['goods_id' => $v->goods_id])->min('sellprice');
                $data[$k]->sellprice = $sellprice;
                if ($sellprice) {
                    $stock = \DB::table('goods_attr')->where([['goods_id', '=', $v->goods_id]])->sum('stock');
                    $data[$k]->stock = $stock;
                }
            }
            return $this->returnMsg(true,0,'查询成功',$data);
        }else{
            return $this->returnMsg(false,101,'暂无数据',[]);
        }
    }
}
