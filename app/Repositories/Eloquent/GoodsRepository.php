<?php
namespace App\Repositories\Eloquent;

use App\Models\Goods;
use App\Models\Commit;
use Carbon\Carbon;
use ClassesWithParents\D;
use Illuminate\Http\Request;
use Cache;
use JWTAuth;
use PhpParser\Node\Scalar\DNumber;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use DB;
use App\Http\Controllers\Web\WechatController;

class GoodsRepository extends Repository
{
    public function model()
    {
        return Goods::class;
    }

    /**
     * 获取规格模型
     * @return \Illuminate\Support\Collection
     * @author: simayubo
     */
    public function getAttrModels()
    {
        $attr_models = DB::table('attr_models')->get();
        return $attr_models;
    }

    /**
     * 添加商品
     * @param $request
     * @return bool
     * @author: simayubo
     */
    public function addGoods($request)
    {
        $all = $request->all();
        $data = [
            'name' => $all['name'],
            'describe' => $all['describe'],
            'category_id' => $all['category_id'],
            'hot' => $all['hot'],
            'tui' => $all['tui'],
            'xian' => $all['xian'],
            'status' => $all['status'],
            'sort' => $all['sort'],
            'postage' => $all['postage'],
            'content' => $all['content'],
            'pic' => trim($all['pic'], ','),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
        if ($all['xian'] == 1) {
            $data['date_star'] = $all['date_star'];
            $data['date_end'] = $all['date_end'];
        }
        //上传缩略图
        if ($request->file('thumb')) {
            $res = upload_file($request->file('thumb'), 'goods', 'image');
            if (!$res['status']) {
                flash($res['error'], 'error');
                return false;
            }
            $data['thumb'] = $res['path'];
        }
        $request_attr = $all['attr'];
        $_attr = [];  //原表.属性id组
        $_attr_value = []; //原表.属性值id组
        $attr_value_goods = [];  //关联表数组

        DB::beginTransaction();
        $status = 1; //初始化状态
        //添加商品
        $goods_id = DB::table('goods')->insertGetId($data);
        if (!$goods_id) {
            $status = 0;
        }
//        dd($request_attr);
        //获取属性及属性值
        foreach ($request_attr as $k => $v) {
            foreach ($v['attr'] as $_k => $_v) {
                if ($_k != 0) {
                    if (!in_array($_v, $_attr)) {
                        $_attr[] = $_v;
                    }
                    if (!in_array($_k, $_attr_value)) {
                        $_attr_value[] = $_k;
                    }
                }
            }
        }
//        dd($_attr);
        //dump($_attr_value);dd();
        //添加属性及属性值
        $attr_array = [];
        $attr_values_array = [];
        //插入新属性
        $_attr_data_obj = DB::table('goods_attr_ibute')->select('id', 'name')->whereIn('id', $_attr)->get()->toArray();
        $_attr_value_data_obj = DB::table('goods_attr_value')->select('id', 'value', 'pid')->whereIn('id', $_attr_value)->get()->toArray();

//        dd($_attr_data_obj);
        foreach ($_attr_data_obj as $v) {
            $_arr = get_object_vars($v);
            $_attr_ibute_id = DB::table('goods_attr_ibute_snap')->insertGetId(['name' => $_arr['name'], 'goods_id' => $goods_id]);
            if (!$_attr_ibute_id) {
                $status = 0;
                break;
            }
            $attr_array[$_arr['id']] = $_attr_ibute_id;
            foreach ($_attr_value_data_obj as $_v) {
                $_arr_value = get_object_vars($_v);
                if ($_arr_value['pid'] == $_arr['id']) {
                    $goods_attr_value_id = DB::table('goods_attr_value_snap')->insertGetId(['value' => $_arr_value['value'], 'goods_id' => $goods_id, 'pid' => $_attr_ibute_id]);
                    if (!$goods_attr_value_id) {
                        $status = 0;
                        break;
                    }
                    $attr_values_array[$_arr_value['id']] = $goods_attr_value_id;
                }
            }
        }
//        dump($attr_values_array);
        //组建数据d
        //获取属性及属性值
        foreach ($request_attr as $k => $v) {
            $_goods_attr = ',';
            foreach ($v['attr_values'] as $_k => $_v) {
                if ($_k == 0) {
                    $_goods_attr = '';
                } else {
//                    dump($_k);
                    $__attr_values_id = $attr_values_array[$_k];
                    $_goods_attr .= $__attr_values_id . ',';
                }
            }
            $attr_value_goods[] = [
                'goods_id' => $goods_id,
                'attr' => $_goods_attr,
                'market_price' => $v['market_price'],
                'sellprice' => $v['sellprice'],
                'chengben_price' => $v['chengben_price'],
                'stock' => $v['stock'],
                'goods_number' => empty($v['goods_number']) ? '' : $v['goods_number'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
        }
        //剔除重复属性
        $attr_value_goods_array = [];

        foreach ($attr_value_goods as $v) {
            if (!empty($attr_value_goods_array)) {
                $__status = 0;
                foreach ($attr_value_goods_array as $_v) {
                    if ($v['attr'] == $_v['attr']) {
                        $__status = 1;
                    }
                }
                if ($__status == 0) {
                    $attr_value_goods_array[] = $v;
                }
            } else {
                $attr_value_goods_array[] = $v;
            }
        }
        //dd($attr_value_goods_array);
        //添加属性，属性值，商品关联表
        $goods_attr = DB::table('goods_attr')->insert($attr_value_goods_array);
        if (!$goods_attr) $status = 0;

        //更新附件状态
        //缩略图
        $img_files = explode(',', trim($data['pic'], ','));
        $img_files[] = $data['thumb'];
        $files_status = set_file_status($img_files, 1);
        if (!$files_status) $status = 0;

        if ($status == 1) {
            DB::commit();
            flash('商品添加成功！', 'success');
            return true;
        } else {
            DB::rollback();
            flash('商品添加失败！', 'error');
            return false;
        }
    }

    /**
     * 获取后台商品列表
     * @param $request
     * @author: simayubo
     */
    public function getAdminGoodsList($request)
    {
        $input = $request->all();
        $where[] = ['goods.status', '>=', -1];
        if (!empty($input['name'])) $where[] = ['goods.name', 'like', '%' . $input['name'] . '%'];
        if (!empty($input['status']) && $input['status'] != 0) $where[] = ['goods.status', '=', $input['status']];
        if (!empty($input['hot']) && $input['hot'] != 0) $where[] = ['goods.hot', '=', $input['hot']];
        if (!empty($input['tui']) && $input['tui'] != 0) $where[] = ['goods.tui', '=', $input['tui']];
        if (!empty($input['xian']) && $input['xian'] != 0) $where[] = ['goods.xian', '=', $input['xian']];
        if (!empty($input['date_star']) && empty($input['date_end'])) {
            $where[] = ['goods.created_at', '>=', $input['date_star']];
        } elseif (empty($input['date_star']) && !empty($input['date_end'])) {
            $where[] = ['goods.created_at', '<=', $input['date_end']];
        } elseif (!empty($input['date_star']) && !empty($input['date_end'])) {
            $where[] = ['goods.created_at', '>=', $input['date_star']];
            $where[] = ['goods.created_at', '<', $input['date_end']];
        }

        $list = $this->model
            ->select('goods.*', 'category.category_name')
            ->where($where)
            ->leftJoin('category', 'goods.category_id', '=', 'category.id')
            ->orderBy('goods.sort', 'desc')
            ->paginate(20);
        $list->appends($input)->render();
        return $list;
    }

    /**
     * 设置商品排序
     * @param Request $request
     * @return bool
     * @author: simayubo
     */
    public function setGoodsSort($request)
    {
        $info = $request->only('goods_id', 'sort');
        $goods = $this->model->find($info['goods_id']);
        $goods->sort = $info['sort'];
        $res = $goods->save();
        if ($res) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 设置商品商品状态
     * @param $goods_id
     * @param $status
     * @return bool
     * @author: simayubo
     */
    public function setGoodsStatus($goods_id, $status)
    {
        $goods = $this->model->find($goods_id);
        if ($goods) {
            $goods->status = $status;
            if ($goods->save()) {
                return true;
            }
        }
        return false;
    }

    /**
     * 删除商品文件
     * @param $path
     * @param $goods_id
     * @return bool
     * @author: simayubo
     */
    public function deleteGoodsImage($path, $goods_id)
    {

        //从商品图册中移除
        $goods_info = DB::table('goods')->where('id', $goods_id)->first();
        $pic_array = explode(',', trim($goods_info->pic, ','));
        foreach ($pic_array as $k => $item) {
            if ($item == $path) {
                unset($pic_array[$k]);
            }
        }
        $last_path = implode(',', $pic_array);
        $res = DB::table('goods')->where('id', $goods_id)->update(['pic' => $last_path]);
        if (!$res) return false;

        //删除files记录
        $row = DB::table('files')->where('path', $path)->delete();
        if (!$row) return false;

        //删除文件
        $dir_path = public_path() . $path;
        unset($dir_path);

        //查询图册
        $res = DB::table('goods')->select('pic')->where('id', $goods_id)->first();
        return $res->pic;
    }

    /**
     * 获取属性值
     * @author: simayubo
     */
    public function getAdminGoodsAttr($id)
    {
        $goods_arrt_list = [];
        $goods_attr = DB::table('goods_attr')->where('goods_id', $id)->get()->toArray();
        foreach ($goods_attr as $item) {
            $_value = get_object_vars($item);
            if ($_value['attr'] == '') {
                $_goods_attr_array[] = [
                    'attr' => 0,
                    'attr_values' => 0
                ];
            } else {
                $_goods_attr_array = [];
                $_goods_attr = explode(',', trim($_value['attr'], ','));
                foreach ($_goods_attr as $_v) {
                    $__attr_value = DB::table('goods_attr_value_snap')->where('id', $_v)->first();
                    $__attr = DB::table('goods_attr_ibute_snap')->where('id', $__attr_value->pid)->first();
                    $_goods_attr_array[] = [
                        'attr' => get_object_vars($__attr),
                        'attr_values' => get_object_vars($__attr_value)
                    ];
                }
            }

            $_value['attr_array'] = $_goods_attr_array;
            $goods_arrt_list[] = $_value;
        }
//        dd($goods_arrt_list);
        return $goods_arrt_list;
    }

    /**
     * 删除商品品属性
     * @param $id
     * @return bool
     * @author: simayubo
     */
    public function deleteGoodsAttr($id)
    {
        $info = DB::table('goods_attr')->find($id);
        $attr_values = explode(',', trim($info->attr, ','));

        $attr_arr = [];
        $attr_values_arr = [];
        if ($info->attr != '') {
            foreach ($attr_values as $item) {
                $_attr_value_count = DB::table('goods_attr')
                    ->where('goods_id', '=', $info->goods_id)
                    ->where('attr', 'like', '%,' . $item . ',%')
                    ->whereNotIn('id', [$id])
                    ->count();
                if ($_attr_value_count <= 0) {
                    $attr_values_arr[] = $item;
                }
            }

            $attr = DB::table('goods_attr_ibute_snap')->where('goods_id', '=', $info->goods_id)->pluck('id')->toArray();
            foreach ($attr as $k => $item) {
                $_value = DB::table('goods_attr_value_snap')
                    ->where('pid', '=', $item)
                    ->pluck('id')->toArray();

                foreach ($_value as $_k => $_v) {
                    if (in_array($_v, $attr_values_arr)) {
                        unset($_value[$_k]);
                    }
                }
                if (empty($_value)) {
                    $attr_arr[] = $item;
                }
            }
        }

        DB::beginTransaction();
        $status = 1;
        $res1 = DB::table('goods_attr')->delete($id);
        if (!$res1) $status = 0;

        if (!empty($attr_arr)) {
            $res2 = DB::table('goods_attr_ibute_snap')->whereIn('id', $attr_arr)->delete();
            if (!$res2) $status = 0;
        }
        if (!empty($attr_values_arr)) {
            $res3 = DB::table('goods_attr_value_snap')->whereIn('id', $attr_values_arr)->delete();
            if (!$res3) $status = 0;
        }

        if ($status == 1) {
            DB::commit();
            return true;
        } else {
            DB::rollBack();
            return false;
        }
    }

    /**
     * 更新商品
     * @param $request
     * @return bool
     * @author: simayubo
     */
    public function updateGoods($request, $all, $id)
    {
        $data = [
            'name' => $all['name'],
            'describe' => $all['describe'],
            'category_id' => $all['category_id'],
            'hot' => $all['hot'],
            'tui' => $all['tui'],
            'xian' => $all['xian'],
            'status' => $all['status'],
            'sort' => $all['sort'],
            'postage' => $all['postage'],
            'content' => $all['content'],
            'pic' => trim($all['pic'], ','),
            'updated_at' => Carbon::now(),
        ];
        if ($all['xian'] == 1) {
            $data['date_star'] = $all['date_star'];
            $data['date_end'] = $all['date_end'];
        }
        //上传缩略图
        if ($request->file('thumb')) {
            $res = upload_file($request->file('thumb'), 'goods', 'image');
            if (!$res['status']) {
                flash($res['error'], 'error');
                return false;
            }
            $data['thumb'] = $res['path'];
        }
        $request_attr = '';
        if (!empty($all['attr'])) {
            $request_attr = $all['attr'];
        }
        $_attr = [];  //原表.属性id组
        $_attr_value = []; //原表.属性值id组
        $attr_value_goods = [];  //关联表数组

        DB::beginTransaction();
        $status = 1; //初始化状态
        //添加商品
        $goods_id = DB::table('goods')->where('id', $id)->update($data);
        if (!$goods_id) {
            $status = 0;
        }
        if (!empty($request_attr)) {
            //获取属性及属性值
            foreach ($request_attr as $k => $v) {
                foreach ($v['attr'] as $_k => $_v) {
                    if ($_k != 0) {
                        if (!in_array($_v, $_attr)) {
                            $_attr[] = $_v;
                        }
                        if (!in_array($_k, $_attr_value)) {
                            $_attr_value[] = $_k;
                        }
                    }
                }
            }

            //添加属性及属性值
            $attr_array = [];
            $attr_values_array = [];
            //插入新属性
            $_attr_data_obj = DB::table('goods_attr_ibute')->select('id', 'name')->whereIn('id', $_attr)->get()->toArray();
            $_attr_value_data_obj = DB::table('goods_attr_value')->select('id', 'value', 'pid')->whereIn('id', $_attr_value)->get()->toArray();

            foreach ($_attr_data_obj as $v) {
                $_arr = get_object_vars($v);
                $_attr_ibute_id = DB::table('goods_attr_ibute_snap')->where(['name' => $_arr['name'], 'goods_id' => $id])->value('id');
                if (!$_attr_ibute_id) {
                    $_attr_ibute_id = DB::table('goods_attr_ibute_snap')->insertGetId(['name' => $_arr['name'], 'goods_id' => $id]);
                    if (!$_attr_ibute_id) {
                        $status = 0;
                        break;
                    }
                }
                $attr_array[$_arr['id']] = $_attr_ibute_id;
                foreach ($_attr_value_data_obj as $_v) {
                    $_arr_value = get_object_vars($_v);
                    if ($_arr_value['pid'] == $_arr['id']) {
                        $goods_attr_value_id = DB::table('goods_attr_value_snap')->where(['value' => $_arr_value['value'], 'goods_id' => $id])->value('id');
                        if (!$goods_attr_value_id) {
                            $goods_attr_value_id = DB::table('goods_attr_value_snap')->insertGetId(['value' => $_arr_value['value'], 'goods_id' => $id, 'pid' => $_attr_ibute_id]);
                            if (!$goods_attr_value_id) {
                                $status = 0;
                                break;
                            }
                        }
                        $attr_values_array[$_arr_value['id']] = $goods_attr_value_id;
                    }
                }
            }
//            dd();
            //组建数据
            //获取属性及属性值
            foreach ($request_attr as $k => $v) {
                $_goods_attr = ',';
                foreach ($v['attr_values'] as $_k => $_v) {
                    if ($_k == 0) {
                        $_goods_attr = '';
                    } else {
                        $__attr_values_id = $attr_values_array[$_k];
                        $_goods_attr .= $__attr_values_id . ',';
                    }
                }
                $attr_value_goods[] = [
                    'goods_id' => $id,
                    'attr' => $_goods_attr,
                    'market_price' => $v['market_price'],
                    'sellprice' => $v['sellprice'],
                    'chengben_price' => $v['chengben_price'],
                    'stock' => $v['stock'],
                    'goods_number' => empty($v['goods_number']) ? '' : $v['goods_number'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
            }
            //剔除重复属性
            $attr_value_goods_array = [];
            foreach ($attr_value_goods as $v) {
                if (!empty($attr_value_goods_array)) {
                    $__status = 0;
                    foreach ($attr_value_goods_array as $_v) {
                        if ($v['attr'] == $_v['attr']) {
                            $__status = 1;
                        }
                    }
                    if ($__status == 0) {
                        $attr_value_goods_array[] = $v;
                    }
                } else {
                    $attr_value_goods_array[] = $v;
                }
            }

            //插入属性，属性值，商品关联表
            $goods_attr = DB::table('goods_attr')->insert($attr_value_goods_array);
            if (!$goods_attr) $status = 0;
        }

        //更新附件状态
        //缩略图
        $img_files = explode(',', trim($data['pic'], ','));
        if (!empty($data['thumb'])) {
            $img_files[] = $data['thumb'];
        }
        $files_status = set_file_status($img_files, 1);
        if (!$files_status) $status = 0;

        //更新已有属性
        $old_attr = $request->only('up_attr');

        if (!empty($old_attr['up_attr'])) {
            foreach ($old_attr['up_attr'] as $key => $item) {
                $_res = DB::table('goods_attr')->where('id', $key)->update($item);
                if ($_res < 0) {
                    $status = 0;
                    break;
                }
            }
        }
        if ($status == 1) {
            DB::commit();
            flash('商品修改成功！', 'success');
            return true;
        } else {
            DB::rollback();
            flash('商品修改失败！', 'error');
            return false;
        }
    }

    /**
     * 删除商品
     * @param $id
     * @return bool
     */
    public function destoryGoods($id)
    {

    }


    /**
     * 搜索商品    删除 不要
     * @param $request
     * @return bool
     * @Author wangyan
     */
    public function getGoodsListByKey($request)
    {
        $input = $request->all();
        if (empty($input['status'])) {
            return false;
        }

        if ($input['status'] == 1) {
            //综合
            $list = $this->model
                ->where('name', 'like', '%' . $input['key'] . '%')
                ->where('status', '=', '1')
                ->orderBy('sort', 'desc')->select('id', 'name', 'thumb', 'sell_num')->paginate(10);
            foreach ($list as $k => $v) {
                $sell_price = DB::table('goods_attr')->where('goods_id', '=', $v['id'])->select('sellprice')->orderBy('sellprice', 'asc')->first();

                $list[$k]['sell_price'] = $sell_price->sellprice;
            }
        }

        if ($input['status'] == 2) {
            //销量
            $list = $this->model
                ->where('name', 'like', '%' . $input['key'] . '%')
                ->where('status', '=', '1')
                ->orderBy('sell_num', 'desc')->select('id', 'name', 'thumb', 'sell_num')->paginate(10);
            foreach ($list as $k => $v) {
                $sell_price = DB::table('goods_attr')->where('goods_id', '=', $v['id'])->select('sellprice')->orderBy('sellprice', 'asc')->first();

                $list[$k]['sell_price'] = $sell_price->sellprice;
            }
        }

        if ($input['status'] == 3) {
            //评价
            $list = $this->model
                ->where('name', 'like', '%' . $input['key'] . '%')
                ->where('status', '=', '1')
                ->orderBy('count_comment', 'desc')->select('id', 'name', 'thumb', 'sell_num', 'count_comment')->paginate(10);
            foreach ($list as $k => $v) {
                $sell_price = DB::table('goods_attr')->where('goods_id', '=', $v['id'])->select('sellprice')->orderBy('sellprice', 'asc')->first();

                $list[$k]['sell_price'] = $sell_price->sellprice;
                unset($list[$k]['count_comment']);
            }
        }

        if ($input['status'] == 4) {
            //价格正序
            $list = $this->model
                ->where('name', 'like', '%' . $input['key'] . '%')
                ->where('status', '=', '1')
                ->select('id', 'name', 'thumb', 'sell_num')->paginate(10);
            foreach ($list as $k => $v) {
                $sell_price = DB::table('goods_attr')->where('goods_id', '=', $v['id'])->select('sellprice')->orderBy('sellprice', 'asc')->first();
                $list[$k]['sell_price'] = $sell_price->sellprice;
            }
            $price = array();
            foreach ($list as $p) {
                $price[] = $p['sell_price'];
            }
            $list = $list->toArray();
            array_multisort($price, SORT_ASC, $list['data']);
        }

        if ($input['status'] == 5) {
            //价格倒序
            $list = $this->model
                ->where('name', 'like', '%' . $input['key'] . '%')
                ->where('status', '=', '1')
                ->select('id', 'name', 'thumb', 'sell_num')->paginate(10);
            foreach ($list as $k => $v) {
                $sell_price = DB::table('goods_attr')->where('goods_id', '=', $v['id'])->select('sellprice')->orderBy('sellprice', 'asc')->first();
                $list[$k]['sell_price'] = $sell_price->sellprice;
            }
            $price = array();
            foreach ($list as $p) {
                $price[] = $p['sell_price'];
            }
            $list = $list->toArray();
            array_multisort($price, SORT_DESC, $list['data']);
        }
        if ($list) {
            return $list;
        } else {
            return false;
        }
    }

    /**
     * 首页商品列表
     * @param $request
     * @return bool
     * @Author wangyan
     */
    public function getGoodsListBySts($request)
    {
        $input = $request->all();
        if (empty($input['status'])) {
            return false;
        }
        if ($input['status'] == 1) $where[] = ['hot', '=', 1];
        if ($input['status'] == 2) $where[] = ['tui', '=', 1];
        if ($input['status'] == 3) $where[] = ['xian', '=', 1];
        if ($input['status'] == 3) {
            $list = $this->model
                ->where('status', '=', '1')
                ->where('date_star', '<=', date('Y-m-d H:i:s', time()))
                ->where('date_end', '>', date('Y-m-d H:i:s', time()))
                ->where($where)
                ->orderBy('sort', 'desc')->select('id', 'name', 'thumb', 'star', 'describe', 'date_star', 'date_end')->get()->toArray();

        } else {
            $list = $this->model->where('status', '=', '1')->where($where)->orderBy('sort', 'desc')->select('id', 'name', 'thumb', 'star', 'describe')->get()->toArray();
        }
        foreach ($list as $k => $v) {
            $sell_price = DB::table('goods_attr')->where('goods_id', '=', $v['id'])->select('sellprice')->orderBy('sellprice', 'asc')->first();
            $list[$k]['sell_price'] = $sell_price->sellprice;
            if ($input['status'] == 3) {
                $list[$k]['now_time'] = time();
                $list[$k]['end_time'] = strtotime($v['date_end']);
                $list[$k]['over_time'] = strtotime($v['date_end']) - time();
            }
        }
        if ($list) {
            return $list;
        } else {
            return false;
        }
    }

    /**
     * 通过分类id（或搜索）获取商品列表
     * @param $request
     * @return int
     * @Author wangyan
     */
    public function getGoodsListByCon($request)
    {
        $input = $request->all();

        $num = 20;
        if (empty($input['status'])) {
            return false;
        }
        if (!empty($input['key'])) {
            $this->addSearch($input['key']);
        }
        $where = [];
        if ($input['category_id'] == 0) {
            $where[] = ['name', 'like', '%' . $input['key'] . '%'];
        }
        if (!empty($input['category_id'])) {
            $where[] = ['category_id', '=', $input['category_id']];
        }
        $where[] = ['status', '=', '1'];
        if ($input['status'] == 1) {
            //综合
            $list = $this->model
                ->where($where)
                ->orderBy('sort', 'desc')->select('id', 'name', 'thumb', 'sell_num')->paginate($num);
            foreach ($list as $k => $v) {
                $sell_price = DB::table('goods_attr')->where('goods_id', '=', $v['id'])->select('sellprice')->orderBy('sellprice', 'asc')->first();
                $list[$k]['sell_price'] = $sell_price->sellprice;
            }
        }
        if ($input['status'] == 2) {
            //销量
            $list = $this->model
                ->where($where)
                ->orderBy('sell_num', 'desc')->select('id', 'name', 'thumb', 'sell_num')->paginate($num);
            foreach ($list as $k => $v) {
                $sell_price = DB::table('goods_attr')->where('goods_id', '=', $v['id'])->select('sellprice')->orderBy('sellprice', 'asc')->first();
                $list[$k]['sell_price'] = $sell_price->sellprice;
            }
        }
        if ($input['status'] == 3) {
            //评价
            $list = $this->model
                ->where($where)
                ->orderBy('count_comment', 'desc')->select('id', 'name', 'thumb', 'sell_num', 'count_comment')->paginate($num);
            foreach ($list as $k => $v) {
                $sell_price = DB::table('goods_attr')->where('goods_id', '=', $v['id'])->select('sellprice')->orderBy('sellprice', 'asc')->first();

                $list[$k]['sell_price'] = $sell_price->sellprice;
                unset($list[$k]['count_comment']);
            }
        }

        if ($input['status'] == 4) {
            //价格正序
            $list = $this->model
                ->where($where)
                ->select('id', 'name', 'thumb', 'sell_num')->paginate($num);

            foreach ($list as $k => $v) {
                $sell_price = DB::table('goods_attr')->where('goods_id', '=', $v['id'])->select('sellprice')->orderBy('sellprice', 'asc')->first();
                $list[$k]['sell_price'] = $sell_price->sellprice;
            }
            $price = array();
            foreach ($list as $p) {
                $price[] = $p['sell_price'];
            }
            $list = $list->toArray();
            array_multisort($price, SORT_ASC, $list['data']);
        }

        if ($input['status'] == 5) {
            //价格倒序
            $list = $this->model
                ->where($where)
                ->select('id', 'name', 'thumb', 'sell_num')->paginate($num);
            foreach ($list as $k => $v) {
                $sell_price = DB::table('goods_attr')->where('goods_id', '=', $v['id'])->select('sellprice')->orderBy('sellprice', 'asc')->first();
                $list[$k]['sell_price'] = $sell_price->sellprice;
            }
            $price = array();
            foreach ($list as $p) {
                $price[] = $p['sell_price'];
            }
            $list = $list->toArray();
            array_multisort($price, SORT_DESC, $list['data']);
        }

        if ($list) {
            return $list;
        } else {
            return false;
        }
    }

    /**
     * 添加搜索
     * @param $key
     * @return bool
     * @author: simayubo
     */
    public function addSearch($key)
    {
        $token = JWTAuth::getToken();
        if (!$token) {
            return false;
        }
        $user = JWTAuth::toUser();
        $count = DB::table('search_history')
            ->where('content', $key)
            ->where('uid', $user->id)
            ->count();
        if ($count <= 0) {
            DB::table('search_history')->insert(['uid' => $user->id, 'content' => $key, 'created_at' => Carbon::now()]);
        }
        return true;
    }

    /**
     * 通过商品id获取商品详情
     * @param $id
     * @Author wangyan
     */
    public function getGoodsInfoById($id)
    {
        $goodsInfo = $this->model->where('id', '=', $id)->first();
        if ($goodsInfo['status'] != 1) {
            return false;
        }

        $_url = 'http://' . $_SERVER['HTTP_HOST'];
        $goodsInfo['content'] = preg_replace("/src=\"\//", 'src="' . $_url . '/', ($goodsInfo['content']));

        //商品轮播图
        $goodsInfo['pic'] = explode(',', $goodsInfo['pic']);

        //价格
        $sell_price = DB::table('goods_attr')->where('goods_id', '=', $goodsInfo['id'])->select('sellprice', 'stock')->orderBy('sellprice', 'asc')->first();
        if (!empty($sell_price)) {
            $goodsInfo['sell_price'] = $sell_price->sellprice;
        }

        //库存
        $goodsInfo['stock'] = DB::table('goods_attr')->where('goods_id', '=', $goodsInfo['id'])->sum('stock');

        //属性
        $attr = DB::table('goods_attr_ibute_snap')->where('goods_id', '=', $goodsInfo['id'])->get();
        if (empty($attr)) {
            return false;
        }
        $goods_attr = [];
        foreach ($attr as $kk => $vv) {
            $goods_attr[$kk]['id'] = $vv->id;
            $goods_attr[$kk]['name'] = $vv->name;
        }
        foreach ($goods_attr as $k => $v) {
            $value = DB::table('goods_attr_value_snap')->where('pid', '=', $v['id'])->get();
            $attr_value = [];
            foreach ($value as $key => $val) {
                $attr_value[$key]['id'] = $val->id;
                $attr_value[$key]['value'] = $val->value;
            }
            $goods_attr[$k]['value'] = $attr_value;
        }
        $goodsInfo['goods_attr'] = $goods_attr;
        //判断是否无属性
        $goodsattr = DB::table('goods_attr')
            ->where([
                ['goods_id', $goodsInfo['id']],
                ['attr', ''],
            ])
            ->value('id');
        if ($goodsattr) {
            $goodsInfo['goods_attr'] = '';
        }

        //json商品属性
        $attr_json = [
            'sellprice' => $sell_price->sellprice,
            'stock' => $sell_price->stock,
            'sys_attrprice' => $this->getAttrJson($id)
        ];
        $goodsInfo['attr_json'] = $attr_json;

        //评论
        $comment = $this->getComment($id);
        if ($comment) {
            $goodsInfo['comment'] = $comment;
        } else {
            $goodsInfo['comment'] = [];
        }

        if ($goodsInfo['xian'] == 1 && $goodsInfo['date_star'] <= date('Y-m-d H:i:s', time()) && $goodsInfo['date_end'] > date('Y-m-d H:i:s', time())) {
            $goodsInfo['now_time'] = time();
            $goodsInfo['end_time'] = strtotime($goodsInfo['date_end']);
            $goodsInfo['over_time'] = strtotime($goodsInfo['date_end']) - time();
        } else {
            $goodsInfo['xian'] = 0;
        }
        unset($goodsInfo['date_star'], $goodsInfo['date_end']);
		
		//判断收藏
        $goodsInfo['goodsKeep'] = 'no';
        $token = JWTAuth::getToken();
        if (!empty($token)) {
            try {
                if (!$user = JWTAuth::parseToken()->authenticate()) {
                    return $goodsInfo;
                }
            } catch (TokenExpiredException $e) {
                return $goodsInfo;
            } catch (TokenInvalidException $e) {
                return $goodsInfo;
            } catch (JWTException $e) {
                return $goodsInfo;
            }
            $userInfo = JWTAuth::toUser($token)->toArray();
            if (!empty($userInfo)) {
                $collect = DB::table('collect')->where(['user_id' => $userInfo['id'], 'goods_id' => $id])->first();
                if (!empty($collect)) {
                    $goodsInfo['goodsKeep'] = 'yes';
                }
            }
        }
		
		
        if ($goodsInfo) {
            return $goodsInfo;
        } else {
            return false;
        }
    }

    /**
     * 通过商品id获取商品属性规格json
     * @param $id
     * @return array
     * @Author wangyan
     */
    protected function getAttrJson($id)
    {

        $list = DB::table('goods_attr')->where('goods_id', '=', $id)->get()->toArray();
        $arr = [];
        foreach ($list as $k => $v) {
            $_arr = get_object_vars($v);
            $attr_str = trim($_arr['attr'], ',');
            $arr[$attr_str] = [
                'sell_price' => $_arr['sellprice'],
                'stock' => $_arr['stock'],
            ];
        }
        return $arr;
    }

    /**
     * 通过商品id评论列表
     * @param $id
     * @return array|bool
     * @Author wangyan
     */
    public function getComment($id)
    {
        $comment = DB::table('commit')
            ->select('commit.content', 'commit.pic', 'commit.star', 'commit.created_at', 'users.nickname', 'users.headimgurl')
            ->where(['commit.goods_id' => $id])
            ->leftJoin('users', 'commit.user_id', '=', 'users.id')
            ->orderBy('commit.created_at', 'desc')
            ->get()->toArray();
        if (empty($comment)) {
            return false;
        } else {
            $commit = [];
            foreach ($comment as $key => $value) {
                $_arr = get_object_vars($value);
                $commit[$key]['nickname'] = $_arr['nickname'];

                if (!preg_match("/^(http:\/\/).*$/", $_arr['headimgurl'])) {
                    $_arr['headimgurl'] = 'http://' . $_SERVER['HTTP_HOST'] . $_arr['headimgurl'];
                }

                $commit[$key]['headimgurl'] = $_arr['headimgurl'];
                $commit[$key]['content'] = $_arr['content'];
                $commit[$key]['star'] = $_arr['star'];
                $commit[$key]['created_at'] = $_arr['created_at'];
                if (!empty($_arr['pic'])) {
                    $pic = $_arr['pic'];
                    $pic_array = explode('@', trim($pic, '@'));

                    $commit[$key]['pic'] = $pic_array;
                }
            }
            return $commit;
        }
    }

    /**
     * 收藏
     * @param $request
     * @return int
     * @Author wangyan
     */
    public function collect($goods_id)
    {

        $userInfo = JWTAuth::toUser()->toArray();
        $collect = DB::table('collect')->where(['user_id' => $userInfo['id'], 'goods_id' => $goods_id])->first();
        if (empty($collect)) {
            $data = [
                'user_id' => $userInfo['id'],
                'goods_id' => $goods_id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
            $result = DB::table('collect')->insert($data);
            if ($result) {
                return 1;
            } else {
                return 1002;
            }
        } else {
            $result = DB::table('collect')->where('id', '=', $collect->id)->delete();
            if ($result) {
                return 2;
            } else {
                return 1002;
            }
        }
    }

    /**
     * 加入购物车
     * @param $goods_id
     * @param $attr_ids
     * @param $num
     * @param $user_id
     * @return int
     * @Author wangyan
     */
    public function addShopping($goods_id, $attr_ids, $num, $user_id)
    {
        $info = $this->getAttrValues($goods_id, $attr_ids);
        $info = get_object_vars($info);
        if (count($info) <= 0 || $info['stock'] < $num) {
            return -1; //库存不足
        }
        $res = DB::table('shopping_cart')
            ->where([
                'goods_id' => $goods_id,
                'goods_attr_id' => $info['id'],
                'user_id' => $user_id,
            ])->first();
        if ($res) {
            $num = $res->num + $num;
            $row = DB::table('shopping_cart')->where(['id' => $res->id])->update(['num' => $num]);
            if ($row > 0) {
                return 1;
            } else {
                return -2; //加入失败
            }
        } else {
            //属性
            $_attr = $info['attr'];
            $attr = '';
            if (!empty($_attr)) {
                $attr_arr = explode(',', trim($info['attr'], ','));
                foreach ($attr_arr as $k => $v) {
                    $attr_value = DB::table('goods_attr_value_snap')->select('value')->where(['id' => $v])->first();
                    $attr .= $attr_value->value . ',';
                }
                $attr = trim($attr, ',');
            }
            $data = [
                'goods_id' => $goods_id,
                'goods_attr_id' => $info['id'],
                'user_id' => $user_id,
                'num' => $num,
                'price' => $info['sellprice'],
                'attr_values' => $attr,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
            $id = DB::table('shopping_cart')->insert($data);
            if ($id > 0) {
                return 1;
            } else {
                return -2; //加入失败
            }
        }
    }

    /**
     * 获取商品属性
     * @param $goods_id
     * @param $attr_ids
     * @return mixed
     * @Author wangyan
     */
    public function getAttrValues($goods_id, $attr_ids)
    {
        $where = ['goods_id' => $goods_id, 'attr' => ',' . $attr_ids . ','];
        if (empty($attr_ids)) {
            $where = ['goods_id' => $goods_id];
        }
        $info = DB::table('goods_attr')->where($where)->first();
        return $info;

    }

    /**直接购买
     * @Author wangyan
     */
    public function buyGoods($goods_id, $attr_ids, $num, $user_id)
    {
        $info = $this->getAttrValues($goods_id, $attr_ids);
        $info = get_object_vars($info);
        if (count($info) <= 0 || $info['stock'] < $num) {
            return -1; //库存不足
        }

        $goods_info = $this->model->where(['id' => $goods_id, 'status' => 1])->first();
        if (count($goods_info) <= 0) {
            return -2; //商品不存在或已下架
        }
        $data = array($goods_id . '_' . $num . '_' . $info['id'] . '_' . $user_id);
        $return = base64_encode(serialize($data));
        return $return;
    }

    /**
     * 订单页面
     * @param $str
     * @param $user_id
     * @Author wangyan
     */
    public function order($str, $user_id, $addr_id)
    {
        if (empty($str)) {
            return false;
        }
        $array = unserialize(base64_decode($str));
        $list = array();

        foreach ($array as $key => $value) {
            $str_array = explode('_', $value);
            $list[$key] = array(
                'goods_id' => $str_array[0],
                'num' => $str_array[1],
                'attr_id' => $str_array[2],
                'user_id' => $str_array[3],
            );
            if (!empty($str_array[4])) {
                $list[$key]['shopping_id'] = $str_array[4];
            }
        }
        $goods_list = [];
        $zong_price = 0;
        $num = 0;
        $postage = 0;
        foreach ($list as $k => $v) {
            $goodsInfo = $this->model
                ->select(
                    'goods.id', 'goods.thumb', 'goods.name', 'goods.postage', 'goods.status',
                    'goods_attr.id as goods_attr_id', 'goods_attr.attr', 'goods_attr.sellprice', 'goods_attr.stock'
                )
                ->where(['goods.id' => $v['goods_id'], 'goods.status' => 1, 'goods_attr.id' => $v['attr_id']])
                ->leftJoin('goods_attr', 'goods_attr.goods_id', '=', 'goods.id')
                ->first();
            if (empty($goodsInfo)) {
                return -1;
            }
            if ($goodsInfo['stock'] < $v['num']) {
                return -2;
            }
            //邮费
            if ($goodsInfo['postage'] >= $postage) {
                $postage = $goodsInfo['postage'];
            }
            //属性
            $_attr = '';
            if ($goodsInfo['attr']) {
                $attr = explode(',', trim($goodsInfo['attr'], ','));
                foreach ($attr as $key => $val) {
                    $attr_value = DB::table('goods_attr_value_snap')->select('value')->where(['id' => $val])->first();
                    $_attr .= $attr_value->value . ',';
                }
            }
            $goodsInfo['attr_value'] = $_attr;
            $goods_list['goods_list'][$k] = $goodsInfo;
            $goods_list['goods_list'][$k]['num'] = $v['num'];
            $goods_list['goods_list'][$k]['price'] = $goodsInfo['sellprice'] * $v['num'];
            $zong_price += $goods_list['goods_list'][$k]['price'];
            $num += $v['num'];

            if (!empty($v['shopping_id'])) {
                $goods_list['goods_list'][$k]['shopping_id'] = $v['shopping_id'];
            }
        }
        $goods_list['price'] = $zong_price;
        $goods_list['postage'] = $postage;
        $goods_list['num'] = $num;
        $goods_list['total_price'] = $zong_price + $postage;
        $default_address = DB::table('shipping_address')->where(['user_id' => $user_id, 'status' => 1])->first();
        $address = DB::table('shipping_address')->where(['user_id' => $user_id])->get()->toArray();
        if (empty($default_address)) {
            if (!empty($address)) {
                $default_address = $address[0];
            } else {
                $default_address = '';
            }
        }
        if (!empty($addr_id)) {
            $default_address = DB::table('shipping_address')->where(['id' => $addr_id])->first();
        }
        if ($default_address != '') {
            $default_address->province = get_city_name($default_address->province);
            $default_address->city = get_city_name($default_address->city);
            $default_address->area = get_city_name($default_address->area);
        }

        $goods_list['default_address'] = $default_address;
        //$goods_list['address'] = $address;
        $goods_list['str'] = $str;
        return $goods_list;
    }

    /**
     *创建订单
     * @Author wangyan
     */
    public function createOrder($data, $user_id, $addr_id, $remark)
    {
        $res = $this->order($data, $user_id, $addr_id);
        if ($res == -1) {
            return -1;
        }
        if ($res == -2) {
            return -2;
        }
        $bili = DB::table('config')->whereIn('id', array(15, 16, 17, 18))->get()->toArray();
        $addr_info = DB::table('shipping_address')->where(['id' => $addr_id])->first();
        $data = [
            'user_id' => $user_id,
            'order_num' => 'SN_' . date('YmdH', time()) . rand(1000, 9999),
            'price' => $res['price'],
            'postage' => $res['postage'],
            'province' => $addr_info->province,
            'city' => $addr_info->city,
            'area' => $addr_info->area,
            'addr' => $addr_info->street . $addr_info->address,
            'postcode' => $addr_info->postcode,
            'phone' => $addr_info->phone,
            'name' => $addr_info->name,
            'pay_price' => $res['total_price'],
            'add_time' => Carbon::now(),
            'remark' => $remark
        ];
        DB::beginTransaction();
        $status = 1; //初始化状态
        $order_id = DB::table('order_info')->insertGetId($data);
        if ($order_id <= 0) {
            $status = 0;
            write_error_logs(['Api/GoodsRepository/createOrder', '订单创建失败']);
        }
        $i = $order_id;
        foreach ($res['goods_list'] as $key => $val) {
            $_data = [
                'order_goods_number' => date('YmdHis', time()) . $i++ . $user_id,
                'order_id' => $order_id,
                'goods_id' => $val['id'],
                'user_id' => $user_id,
                'price' => $val['sellprice'],
                'num' => $val['num'],
                'goods_name' => $val['name'],
                'goods_thumb' => $val['thumb'],
                'goods_attr_values' => $val['attr_value'],
                'goods_attr_id' => $val['goods_attr_id'],
                'postage' => $val['postage'],
                'one_bili' => $bili[0]->value,
                'two_bili' => $bili[1]->value,
                'qu_bili' => $bili[2]->value,
                'vip_bili' => $bili[3]->value,
            ];
            $order_goods__id = DB::table('order_goods')->insert($_data);
            if ($order_goods__id <= 0) {
                $status = 0;
                write_error_logs(['Api/GoodsRepository/createOrder', '商品订单创建失败']);
            }
            //减库存
            $goods_attr = DB::table('goods_attr')->where(['id' => $val['goods_attr_id']])->first();
            $stock = $goods_attr->stock - $val['num'];
            $row = DB::table('goods_attr')->where(['id' => $val['goods_attr_id']])->update(['stock' => $stock]);
            if ($row <= 0) {
                $status = 0;
                write_error_logs(['Api/GoodsRepository/createOrder', '库存减少失败']);
            }

            //删除购物车
            if (!empty($val['shopping_id'])) {
                $_shopping = DB::table('shopping_cart')->where(['id' => $val['shopping_id']])->delete();
                if ($_shopping <= 0) {
                    $status = 0;
                    write_error_logs(['Api/GoodsRepository/createOrder', '购物车删除失败']);
                }
            }

        }
        if ($status == 1) {
            DB::commit();
            return ['order_id' => $order_id];
        } else {
            DB::rollback();
            return -3;
        }

    }

    /**
     * 购物车结算
     * @param $user_id
     * @param $data
     * @return array|int
     * @Author wangyan
     */
    public function shoppingCart($user_id, $data)
    {
        $shop_list_arr = explode(',', $data);
        $shop_list = [];
        foreach ($shop_list_arr as $k => $v) {
            $str_arr = explode('_', $v);
            $shop_id = $str_arr[0];
            $num = $str_arr[1];
            $shop_list[] = [
                'shop_id' => $shop_id,
                'num' => $num,
            ];
        }
        $data = [];
        foreach ($shop_list as $key => $val) {
            $shopping_info = DB::table('shopping_cart')
                ->select('shopping_cart.*', 'goods_attr.stock')
                ->where(['shopping_cart.id' => $val['shop_id'], 'goods.status' => 1])
                ->leftJoin('goods_attr', 'goods_attr.id', '=', 'shopping_cart.goods_attr_id')
                ->leftJoin('goods', 'goods.id', '=', 'shopping_cart.goods_id')
                ->first();
            if (empty($shopping_info)) {
                return -1;
            }
            if ($val['num'] > $shopping_info->stock) {
                return -2;//库存不足
            }
            $data[] = $shopping_info->goods_id . '_' . $val['num'] . '_' . $shopping_info->goods_attr_id . '_' . $user_id . '_' . $shopping_info->id;
        }
        $return = base64_encode(serialize($data));
        return $return;
    }

    /**
     * 订单结算页面
     * @Author wangyan
     */
    public function payOrder($user_id, $order_id)
    {
        $order_info = DB::table('order_info')->where(['id' => $order_id])->first();
        if ($order_info->user_id != $user_id) {
            return -1;
        }
        if ($order_info->pay_status > 0) {
            return -2;
        }
        $data = [
            'order_id' => $order_info->id,
            'order_num' => $order_info->order_num,
            'pay_price' => $order_info->pay_price,

        ];
        return $data;
    }

    /**
     * 支付
     * @param $order_id
     * @param $password
     * @param $user_id
     * @Author wangyan
     */
    public function pay($order_id, $password, $user_id, $pay_type, $flg = 1)
    {
        $order_info = DB::table('order_info')->where(['id' => $order_id])->first();
        $order_info = get_object_vars($order_info);
        if ($order_info['user_id'] != $user_id) {
            return -1;//支付用户与订单用户不一致
        }

        if ($order_info['status'] > 0) {
            return -5;
        }
        $user_info = DB::table('users')->where(['id' => $user_id])->first();
        if ($flg) {
            if (empty($user_info->pay_pwd)) {
                return -6; //未设置支付密码
            }

            $is_check = \Hash::check($password, $user_info->pay_pwd);
            if (!$is_check) {
                return -2; //登录密码错误
            }

            if ($order_info['pay_price'] > $user_info->wallet) {
                return -3;
            }
        }
        DB::beginTransaction();
        $status = 1; //初始化状态
        $data = [
            'pay_status' => 1,
            'pay_type' => $pay_type,
            'status' => 1,
            'pay_time' => Carbon::now(),
        ];
        $res1 = DB::table('order_info')->where(['id' => $order_id])->update($data);
        if ($res1 <= 0) {
            $status = 0;
            write_error_logs(['Api/GoodsRepository/pay', '订单状态修改失败']);
        }
        unset($data['pay_type']);
        $res2 = DB::table('order_goods')->where(['order_id' => $order_id])->update($data);
        if ($res2 <= 0) {
            $status = 0;
            write_error_logs(['Api/GoodsRepository/pay', '商品订单状态修改失败']);
        }


        if ($flg) {
            $wallet = [
                'wallet' => $user_info->wallet - $order_info['pay_price'],
                'use_wallet' => $user_info->use_wallet + $order_info['pay_price'],
            ];
            $res4 = DB::table('users')->where(['id' => $user_id])->update($wallet);
            if ($res4 <= 0) {
                $status = 0;
                write_error_logs(['Api/GoodsRepository/pay', '用户余额修改失败']);
            }
        }
		$wallet = DB::table('users')->where(['id' => $user_id])->value('wallet');
        if ($flg) {
            $_data = [
                'user_id' => $user_id,
                'money' => $order_info['pay_price'],
                'surplus_money'  =>  $wallet,
                'use' => '支付订单【' . $order_info['order_num'] . '】花费余额',
                'status' => -1,
                'updated_at' => Carbon::now(),
                'created_at' => Carbon::now(),
            ];
        } else {
            $_data = [
                'user_id' => $user_id,
                'money' => $order_info['pay_price'],
				'surplus_money'  =>  $wallet,
                'use' => '微信支付订单【' . $order_info['order_num'] . '】',
                'status' => -1,
                'updated_at' => Carbon::now(),
                'created_at' => Carbon::now(),
            ];
        }
        $res3 = DB::table('consumption_record')->insert($_data);
        if ($res3 <= 0) {
            $status = 0;
            write_error_logs(['Api/GoodsRepository/pay', '用户消费记录添加失败']);
        }

        if ($status == 1) {
            DB::commit();
            $wechat = new WechatController();
            $wechat->sendTemplateMessage($order_id, 1);
            return 1;
        } else {
            DB::rollback();
            if (!$flg) {
                write_error_logs(['Api/GoodsRepository/pay', $order_info['id'] . '用户微信支付失败']);
            }
            return -4;
        }
    }


}