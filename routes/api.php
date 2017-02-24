<?php



/*

|--------------------------------------------------------------------------

| API Routes

|--------------------------------------------------------------------------

|

| Here is where you can register API routes for your application. These

| routes are loaded by the RouteServiceProvider within a group which

| is assigned the "api" middleware group. Enjoy building your API!

|

*/

/**

 * api

 */



$api = app('Dingo\Api\Routing\Router');

$api->version('v1_0', function ($api) {

    $api->group(['namespace' => 'App\Http\Controllers\Api\v1_0', 'middleware' => ['jwt.auth']], function ($api) {

        $api->get('user_address', 'UserController@addressList'); //用户收货地址列表

        $api->get('user_collect', 'CollectController@collectList'); //用户收藏列表

        $api->resource('collect', 'CollectController'); //删除收藏记录

        $api->get('income', 'WithdrawController@userIncome'); //用户收益

        $api->resource('withdraw', 'WithdrawController'); //提现

        $api->resource('address', 'ShippingAddressController'); //收货地址 资源路由

        $api->post('feedback', 'UserController@feedBack'); //提交意见反馈

        $api->get('consume', 'UserController@consumeDetail'); //消费明细

        $api->get('rebate', 'UserController@rebate'); //收益明细

        $api->get('myChild', 'UserController@myChild'); //我的云粉

        $api->get('getQrcode', 'UserController@getQrcode'); //我的二维码



        $api->resource('users', 'UserController');

        $api->get('user', 'UserController@getUserInfo'); //获取用户资料

        $api->get('userList', 'UserController@index'); //用户列表

        $api->any('updatePwd', 'UserController@update');//修改密码

        $api->any('savePayPwd', 'UserController@savePayPwd');//修改支付密码

        $api->post('updateUserInfo', 'UserController@updateUserInfo'); //修改用户个人资料

        //收藏商品

        $api->get('collect', 'GoodsController@collect');

        //购物车列表

        $api->get('shopcar', 'UserController@shopcar');

        //加入购物车

        $api->post('add_car', 'GoodsController@addCar');

        $api->get('del_car', 'UserController@destroyCarById');

        //直接购买

        $api->post('buy', 'GoodsController@buy');

        //订单页面

        $api->get('order', 'GoodsController@order');

        //创建订单

        $api->post('create_order', 'GoodsController@createOrder');

        //购物车结算

        $api->get('shopping_cart', 'GoodsController@shoppingCart');

        //订单支付页面

        $api->get('pay_order', 'GoodsController@payOrder');

        //支付

        $api->post('pay', 'GoodsController@pay');

        //订单列表

        $api->get('orderList', 'OrderController@getOrderList');

        //订单删除

        $api->get('orderDel/{id}', 'OrderController@orderDel');

        //改变订单状态

        $api->get('changeStatus/{id}/{status}', 'OrderController@changeStatus');

        //上传图片

        $api->post('uploadImg', 'OrderController@uploadImg');

        //删除图片

        $api->get('delImg', 'OrderController@delImg');

        //删除退款退货

        $api->get('refundDel/{id}', 'OrderController@refundDel');

        //物流查询

        $api->get('wuLiu/{id}', 'OrderController@wuLiu');

        //物流查询

        $api->get('refundList', 'OrderController@refundList');

        //填写退货信息

        $api->post('saveRefund', 'OrderController@saveRefund');

        //退货退款原因列表

        $api->get('getReason', 'OrderController@getReason');

        //购物车统计

        $api->get('getShopcarCount', 'UserController@getShopcarCount');

        //订单统计

        $api->get('getOrderCount', 'UserController@getOrderCount');

        $api->post('getPhoneCode', 'UserController@getPhoneCode');

        $api->post('checkCode', 'UserController@checkCode');

        $api->get('search/history', 'SearchController@index'); //获取搜索历史
        $api->delete('search/history', 'SearchController@destroy'); //删除搜索历史

        //商品

        $api->resource('goods', 'GoodsController');



        //验证token

        $api->get('checkToken', function (){

            return ['success' => true];

        });

    });

    $api->group(['namespace' => 'App\Http\Controllers\Api\v1_0'], function ($api) {

        $api->post('login', 'AuthController@login'); //用户登录

        $api->post('register', 'AuthController@register'); //用户注册

        //商品分类

        $api->resource('category', 'CategoryController');



        //商品属性

        $api->get('goods_attr/{id}', 'GoodsController@getAttrJson');



        $api->get('region', 'AboutController@regionList'); //获取省市区信息



        //首页商品列表

        $api->get('goods_list', 'IndexController@goodsList');

        //搜索

        $api->get('search', 'IndexController@search');

        //首页banner图

        $api->get('banner', 'IndexController@banner');



        $api->get('about', 'AboutController@about'); //获取个人中心联系我们

        $api->get('hot_tel', 'AboutController@hotTel'); //获取设置中心客服热线

        $api->get('helpList', 'AboutController@helpList'); //帮助中心

        $api->get('notice', 'AboutController@notice'); //系统公告

        $api->get('index_notice', 'AboutController@indexNotice'); //首页公告

        $api->get('notice_detail', 'AboutController@noticeDetail'); //文章详情

        $api->get('withdraw_intro', 'AboutController@withdraw'); //提现说明

        $api->get('bonus', 'AboutController@bonus'); //奖励规则



        $api->post('refresh', ['middleware' => 'jwt.refresh']);//刷新token

        $api->get('get_region_name', 'ShippingAddressController@getNameById');//通过id获取地址名称

        //物流公司列表

        $api->get('getWuliu', 'OrderController@getWuliu');









        //测试--获取token

        $api->get('login2', 'AuthController@login2');

    });

});