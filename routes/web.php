<?php



/*

|--------------------------------------------------------------------------

| Web Routes

|--------------------------------------------------------------------------

|

| Here is where you can register web routes for your application. These

| routes are loaded by the RouteServiceProvider within a group which

| contains the "web" middleware group. Now create something great!

|

*/



//前台

Route::group(['namespace' => 'Web','prefix' => '/' ,'middleware' => ['web']],function(){

    Route::get('/', 'HomeController@index');

    Route::get('getPhone', 'HomeController@getPhone');

    Route::resource('article', 'ArticlesController');

    Route::resource('logs', 'LogsController');



});



Route::any('wxlogin', 'Web\WechatController@index');
Route::any('ss', 'Web\WechatController@ss');

Route::get('creatMenus', 'Web\WechatController@creatMenus');

Route::get('auth', 'Web\WechatController@auth');

Route::any('openid_returnUrl/url/{url}','Web\WechatController@openid_returnUrl');

Route::get('apilogin/','Web\WechatController@login');

Route::get('access_token/','Web\WechatController@access_token');

Route::get('jsApi','Web\WechatController@jsApi');

Route::any('notify','Web\WechatController@notify');

//认证

Auth::routes();

Route::get('logout', 'Auth\LoginController@logout');



Route::get('admin/login', 'Admin\LoginController@showLoginForm')->name('admin.login');

Route::post('admin/login', 'Admin\LoginController@login');

Route::get('admin/logout', 'Admin\LoginController@logout');

//后台

Route::group(['namespace' => 'Admin','prefix' => 'admin' ,'middleware' => ['auth.admin']],function(){

    Route::get('/', 'IndexController@index');

    Route::get('/test', 'IndexController@test');

    Route::any('/uploadGoodsImages', 'GoodsController@uploadGoodsImages');

    Route::get('/get_order_count', 'IndexController@getOrderCount');

    //菜单

    Route::resource('menu', 'MenuController');

    //权限组

    Route::resource('role', 'RoleController');

    //权限节点

    Route::resource('permission', 'PermissionController');

    //管理员

    Route::resource('admin', 'AdminController');

    //用户

    Route::resource('user', 'UserController');

    Route::post('user/lock', 'UserController@lock'); //禁用账号

    Route::get('get_user_address', 'UserController@getUserAddress'); //收货地址

    Route::get('userChild', 'UserController@userChild'); //下级用户

    Route::post('area','UserController@area'); //获取地区信息(省市区)

    Route::get('agency_set_list/{user_id}','UserController@agencySetList'); //用户代理列表

    Route::any('agency_set/{id}','UserController@agencySet'); //设置代理

    Route::get('agency','UserController@agencyList'); //代理列表

    Route::any('agency_undo/{user_id}','UserController@agencyUndo'); //取消代理

    Route::any('userFanli','UserController@userFanli'); //提交代理设置
	Route::any('agencyFanli','UserController@agencyFanli'); //代理返利列表
    Route::get('userMingxi','UserController@userMingxi'); //余额明细
    //商品

    Route::resource('goods', 'GoodsController');

    Route::get('goodsTopOrDown', 'GoodsController@goodsTopOrDown'); //上下架

    Route::post('set_goods_sort', 'GoodsController@setGoodsSort'); //排序

    Route::post('delete_goods_image', 'GoodsController@deleteGoodsImage'); //删除商品图片

    Route::get('delete_goods_attr/{id}/{goods_id}', 'GoodsController@deleteGoodsAttr'); //删除商品属性
	
	Route::get('commit/{id}', 'GoodsController@goodsCommit');//商品评论
    Route::get('delete_commit', 'GoodsController@deleteGoodsCommit');//商品评论删除

    //退换货理由

    Route::resource('reason', 'ReasonController');

    //商品分类

    Route::resource('category', 'CategoryController');

    Route::get('category/getCategoryListById/{id}', 'CategoryController@getCategoryListById');

    Route::get('category/getCategoryChildListById/{id}', 'CategoryController@getCategoryChildListById');

    //订单

    Route::resource('order', 'OrderController');

    Route::get('cancel_order/{id}', 'OrderController@cancelOrder'); //取消订单

    Route::any('order_fahuo/{id}', 'OrderController@fahuo'); //发货

    Route::any('edit_wuliu/{id}', 'OrderController@editWuliu'); //编辑物流

    //订单导出

    Route::get('excel/export_order','ExcelController@exportOrder');

//    Route::get('excel/import','ExcelController@import');

    //订单售后

    Route::get('refund', 'RefundController@index');

    Route::get('refund/{id}', 'RefundController@show');

    Route::get('refund_shenhe/{id}', 'RefundController@shenhe');

    Route::get('refund_jujue/{id}', 'RefundController@jujue');

    Route::get('refund_shouhuo/{id}', 'RefundController@shouhuo');

    Route::get('refund_jujue_shouhuo/{id}', 'RefundController@jujueShouhuo');

    //规格模型

    Route::resource('attr_models', 'AttrModelsController');

    Route::get('get_attr_models/{id}', 'AttrModelsController@getAttrModels');

    Route::post('get_attr_models/get_values/{num}', 'AttrModelsController@getAttrAndValues');

    Route::post('edit_attr_models/add_attr_value', 'AttrModelsController@addAttrValue');  //添加模型属性值

    Route::post('edit_attr_models/delete_attr_value', 'AttrModelsController@delAttrValue');  //删除模型属性值

    Route::post('edit_attr_models/delete_attr', 'AttrModelsController@delAttr');  //删除模型属性

    //文章分类

    Route::resource('article_category', 'ArticleCategoryController');

    //文章

    Route::resource('article', 'ArticleController');

    //广告

    Route::resource('guang', 'GuangController');

//    Route::get('ad/ad_position_edit/{id}', 'AdController@editPositionById');

    Route::get('guang/guang/{id}', 'GuangController@guang');

    //欢迎图

    Route::resource('image', 'ImageController');

    //系统配置

    Route::resource('sys', 'SysController');

    Route::post('sys/updateSys', 'SysController@updateSys');

    //意见反馈

    Route::resource('feedback','FeedbackController');

    Route::get('feedbackDeal','FeedbackController@deal'); //处理意见反馈

    //物流公司

    Route::resource('logistics', 'LogisticsController');

    //提现申请

    Route::post('withdraw/deal','WithdrawController@deal'); //处理提现申请

    Route::get('withdraw/refuse','WithdrawController@refuse'); //拒绝提现申请

    Route::post('withdraw/refuse_reason','WithdrawController@refuseReason'); //拒绝提现申请

    Route::resource('withdraw','WithdrawController');

});



