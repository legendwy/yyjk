<?php



namespace App\Http\Controllers\Api\v1_0;



use App\Http\Controllers\Web\WechatController;
use Illuminate\Http\Request;

use App\Http\Controllers\Api\BaseController;

use App\Repositories\Eloquent\GoodsRepository;

use JWTAuth;





class GoodsController extends BaseController

{

    protected $goods;



    public function __construct(GoodsRepository $goods)

    {

        $this->goods = $goods;

    }



    /**

     * 通过商品二级分类获取商品列表

     * @param Request $request

     * @return mixed

     * @Author wangyan

     */

    public function index(Request $request)

    {

        $list = $this->goods->getGoodsListByCon($request);

        if($list){

            return $this->returnMsg(true, 0, 'success', $list);

        }else{

            return $this->returnMsg(false, 1001, '暂无商品');

        }



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
     * 商品详情页
     * @param $id
     * @return mixed
     * @Author wangyan
     */
    public function show($id, Request $request)
    {
        $wechat = new WechatController();
        $userInfo = JWTAuth::toUser()->toArray();
        if (empty($id)) {
            $this->returnMsg(false, 1001, '缺少商品id');
        }
        //确定上下级
        $user_pid = $request->get('user_pid', 0);
        if ($user_pid) {
            $wechat->bingPid($user_pid, $userInfo['id']);
        }
        $goodsInfo = $this->goods->getGoodsInfoById($id);
        if (!$goodsInfo) {
            return $this->returnMsg(false, 1002, '商品不存在或已下架');
        } else {
            //获取JsSign
            $signPackage = $wechat->getJsSign($id, $userInfo['id'],$request->get('url'));
            $goodsInfo['sign'] = $signPackage;
            return $this->returnMsg(true, 0, 'success', $goodsInfo);
        }
    }



    /**

     * 收藏

     * @param Request $request

     * @return mixed

     * @Author wangyan

     */

    public function collect(Request $request){

        $goods_id = $request->get('goods_id');

        if(empty($goods_id)){

            return $this->returnMsg(false,1001,'缺少参数goods_id(商品id)）');

        }

        $result = $this->goods->collect($goods_id);

        if($result == 1){

            return $this->returnMsg(true, 0, '收藏成功',['status'=>1]);

        }

        if($result == 2){

            return $this->returnMsg(true, 0, '取消收藏成功',['status'=>-1]);

        }

        if($result == 1002){

            return $this->returnMsg(false, 1002, '操作失败');

        }

    }



    /**

     * 加入购物车

     * @param Request $request

     * @return mixed

     * @Author wangyan

     */

    public function addCar(Request $request){

        $input = $request->all();

        if(empty($input['goods_id'])){

            return $this->returnMsg(false, 1001, '缺少商品id');

        }

        if(empty($input['num']) || $input['num']<=0){

            return $this->returnMsg(false, 1002, '缺少num(数量)参数或数量必须大于0');

        }

        $userInfo = JWTAuth::toUser()->toArray();

        if($userInfo['status'] == 0){

            return $this->returnMsg(false, 1003, '此账号已被禁用，禁止操作');

        }

        $res = $this->goods->addShopping($input['goods_id'], $input['attr_ids'], $input['num'], $userInfo['id']);

        if($res == 1){

            return $this->returnMsg(true, 0, '加入成功');

        }elseif($res == -1){

            return $this->returnMsg(false,1004,'库存不足');

        }elseif($res == -2){

            return $this->returnMsg(false,1005,'加入购物车失败');

        }

    }



    /**

     * 直接购买

     * @Author wangyan

     */

    public function buy(Request $request){

        $input = $request->all();

        if(empty($input['goods_id'])){

            return $this->returnMsg(false, 1001, '缺少商品id');

        }

        if(empty($input['num']) || $input['num']<=0){

            return $this->returnMsg(false, 1002, '缺少num(数量)参数或数量必须大于0');

        }

        $userInfo = JWTAuth::toUser()->toArray();

        if($userInfo['status'] == 0){

            return $this->returnMsg(false, 1003, '此账号已被禁用，禁止操作');

        }

        $res = $this->goods->buyGoods($input['goods_id'], $input['attr_ids'], $input['num'], $userInfo['id']);

        if($res == -1){

            return $this->returnMsg(false,1001,'库存不足');

        }elseif($res == -2){

            return $this->returnMsg(false, 1002, '商品不存在或已下架');

        }else{

            return $this->returnMsg(true, 0, 'success', $res);

        }

    }



    /**

     * 订单页面

     * @Author wangyan

     */

    public function order(Request $request){

        $input = $request->all();

        if(empty($input['data'])){

            return $this->returnMsg(false, 1001, '参数错误');

        }

        $userInfo = JWTAuth::toUser()->toArray();

        $res = $this->goods->order($input['data'],$userInfo['id'],$input['addr_id']);

        if($res == -1){

            return $this->returnMsg(false,1001,'存在已删除或已下架商品');

        }elseif ($res == -2){

            return $this->returnMsg(false,1001,'存在库存不足的商品');

        }else{

            return $this->returnMsg(true, 0, 'success', $res);

        }

    }



    /**

     * 创建订单

     * @Author wangyan

     */

    public function createOrder(Request $request){

        $input = $request->all();

        if(empty($input['data'])){

            return $this->returnMsg(false, 1001, '参数错误');

        }

        if(empty($input['addr'])){

            return $this->returnMsg(false, 1002, '请选择收货地址');

        }

        $count = \DB::table('shipping_address')->where(['id' => $input['addr']])->count();

        if($count<=0){

            return $this->returnMsg(false, 1003, '收货地址不存在');

        }

        if(empty($input['remark'])){

            $remark = '';

        }else{

            $remark = $input['remark'];

        }



        $userInfo = JWTAuth::toUser()->toArray();

        if($userInfo['status'] == 0){

            return $this->returnMsg(false, 1003, '此账号已被禁用，禁止操作');

        }

        $res = $this->goods->createOrder($input['data'], $userInfo['id'], $input['addr'], $remark);

        if($res == -1){

            return $this->returnMsg(false,1004,'存在已删除或已下架商品');

        }elseif ($res == -2){

            return $this->returnMsg(false,1005,'存在库存不足的商品');

        }elseif ($res == -3){

            return $this->returnMsg(false,1006,'订单创建失败');

        }else{

            return $this->returnMsg(true, 0, 'success', $res);

        }

    }



    /**

     * 购物车结算

     * @Author wangyan

     */

    public function shoppingCart(Request $request){

        $input = $request->all();

        if(empty($input['data'])){

            return $this->returnMsg(false, 1001, '缺少参数（data）购物车id和数量拼接的字符串（id_数量,id_数量），例：1_2,2_3,5_10');

        }

        $userInfo = JWTAuth::toUser()->toArray();

        if($userInfo['status'] == 0){

            return $this->returnMsg(false, 1004, '此账号已被禁用，禁止操作');

        }

        $res = $this->goods->shoppingCart($userInfo['id'],$input['data']);

        if($res == -1){

            return $this->returnMsg(false, 1002, '存在已下架或者已删除的商品');

        }elseif ($res == -2){

            return $this->returnMsg(false, 1003, '存在库存不足的商品');

        }else{

            return $this->returnMsg(true, 0, 'success', $res);

        }

    }



    /**

     * 订单支付页面

     * @Author wangyan

     */

    public function payOrder(Request $request){

        $input = $request->all();

        if(empty($input['id'])){

            return $this->returnMsg(false, 1001, '缺少订单id');

        }

        $userInfo = JWTAuth::toUser()->toArray();

        if($userInfo['status'] == 0){

            return $this->returnMsg(false, 1002, '此账号已被禁用，禁止操作');

        }

        $res = $this->goods->payOrder($userInfo['id'], $input['id']);

        if($res == -1){

            return $this->returnMsg(false, 1003, '支付用户与订单用户id不一致');

        }elseif ($res == -2){

            return $this->returnMsg(false, 1004, '订单已支付，请勿重新支付');

        }else{

            return $this->returnMsg(true, 0, 'success', $res);

        }

    }



    /**

     * 余额支付

     * @Author wangyan

     */

    public function pay(Request $request){

        $input = $request->all();

        $userInfo = JWTAuth::toUser()->toArray();

        if($userInfo['status'] == 0){

            return $this->returnMsg(false, 1001, '此账号已被禁用，禁止操作');

        }

        if(empty($input['pay_type'])){

            return $this->returnMsg(false, 1002, '缺少参数pay_type(支付方式)');

        }

        if(empty($input['order_id'])){

            return $this->returnMsg(false, 1002, '缺少参数order_id(订单id)');

        }

        if(empty($input['password'])){

            return $this->returnMsg(false, 1003, '缺少参数password(支付密码)');

        }

        $res = $this->goods->pay($input['order_id'], $input['password'], $userInfo['id'], $input['pay_type']);

        if($res == -1){

            return $this->returnMsg(false, 1004, '支付用户与订单用户不一致');

        }elseif ($res == -2){

            return $this->returnMsg(false, 1005, '支付密码错误');

        }elseif ($res == -3){

            return $this->returnMsg(false, 1006, '余额不足');

        }elseif ($res == -4){

            return $this->returnMsg(false, 1007, '支付失败');

        }elseif ($res == -5){

            return $this->returnMsg(false, 1008, '不是待支付订单');

        }elseif ($res == -6){

            return $this->returnMsg(false, 1008, '支付密码未设置');

        }else{
            $wechat = new WechatController();
            $wechat->sendTemplateMessage($input['order_id'], 1);
            return $this->returnMsg(true, 0, 'success');

        }

    }



    /**

     * Show the form for editing the specified resource.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function edit($id)

    {

        if(empty($id)){$this->returnMsg(false, 1001, '缺少商品id');}

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

