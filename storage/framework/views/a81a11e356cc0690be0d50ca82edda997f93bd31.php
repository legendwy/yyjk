<?php $__env->startSection('title', '商品列表'); ?>
<?php $__env->startSection('content'); ?>
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>商品管理</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="<?php echo e(url('admin')); ?>">控制台</a>
                </li>
                <li class="active">
                    <strong>商品列表</strong>
                </li>
            </ol>
        </div>
        <div class="col-lg-2">
            <?php if (\Entrust::can('goods.add')) : ?>
            <h2><a class="btn btn-primary btn-outline" href="<?php echo e(url('admin/goods/create')); ?>">添加商品</a></h2>
            <?php endif; // Entrust::can ?>
        </div>
    </div>
    <div class="wrapper wrapper-content  animated">
        
        
        
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content text-center">
                        <form role="form" action="<?php echo e(url('admin/goods')); ?>" class="form-inline" method="get">
                            <div class="form-group">
                                <input type="text" placeholder="商品名称" name="name"
                                       value="<?php echo e(old('name', request()->get('name'))); ?>" class="form-control">
                            </div>
                            <div class="form-group">
                                <div class="input-group date">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text" placeholder="添加时间开始" class="form-control" name="date_star"
                                           id="date_star" value="<?php echo e(old('date_star', request()->get('date_star'))); ?>">
                                </div>
                            </div>
                            <div class="form-group date">
                                <div class="input-group date">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text" placeholder="添加时间截止" class="form-control "
                                           value="<?php echo e(old('date_end', request()->get('date_end'))); ?>" name="date_end"
                                           id="date_end">
                                </div>
                            </div>
                            <div class="form-group">
                                <select class="form-control" name="status">
                                    <option value="0" <?php if(request()->get('status') == 0): ?> selected <?php endif; ?>>全部（上下架）
                                    </option>
                                    <option value="1" <?php if(request()->get('status') == 1): ?> selected <?php endif; ?>>上架</option>
                                    <option value="-1" <?php if(request()->get('status') == -1): ?> selected <?php endif; ?>>下架</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <select class="form-control" name="hot">
                                    <option value="0" <?php if(request()->get('hot') == 0): ?> selected <?php endif; ?>>全部（热门）</option>
                                    <option value="1" <?php if(request()->get('hot') == 1): ?> selected <?php endif; ?>>热门</option>
                                    <option value="-1" <?php if(request()->get('hot') == -1): ?> selected <?php endif; ?>>非热门</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <select class="form-control" name="tui">
                                    <option value="0" <?php if(request()->get('tui') == 0): ?> selected <?php endif; ?>>全部（推荐）</option>
                                    <option value="1" <?php if(request()->get('tui') == 1): ?> selected <?php endif; ?>>推荐</option>
                                    <option value="-1" <?php if(request()->get('tui') == -1): ?> selected <?php endif; ?>>非推荐</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <select class="form-control" name="xian">
                                    <option value="0" <?php if(request()->get('xian') == 0): ?> selected <?php endif; ?>>全部（限时）</option>
                                    <option value="1" <?php if(request()->get('xian') == 1): ?> selected <?php endif; ?>>限时</option>
                                    <option value="-1" <?php if(request()->get('xian') == -1): ?> selected <?php endif; ?>>非限时</option>
                                </select>
                            </div>
                            <button class="btn btn-success" type="submit">搜索</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <?php echo $__env->make('flash::message', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                        <table class="table table-hover ">
                            <thead>
                            <tr>
                                <th>#ID</th>
                                <th>排序</th>
                                <th width="380">商品名称</th>
                                <th>缩略图</th>
                                <th>分类</th>
                                <th>邮费</th>
                                <th>销售量</th>
                                <th>评论次数</th>
                                <th>添加时间</th>
                                <th>更新时间</th>
                                <th>热门</th>
                                <th>推荐</th>
                                <th>限时</th>
                                <th>状态</th>
                                <th width="120">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                <tr>
                                    <td><?php echo e($item->id); ?></td>
                                    <td><input type="text" value="<?php echo e($item->sort); ?>" aid="<?php echo e($item->id); ?>"
                                               class="form-control set-sort" style="width: 80px;"></td>
                                    <td><?php echo e($item->name); ?></td>
                                    <td><img src="<?php echo e($item->thumb); ?>"
                                             style="width: 35px; height: 35px; border-radius: 50%"></td>
                                    <td><?php echo e($item->category_name); ?></td>
                                    <td><?php echo e($item->postage); ?></td>
                                    <td><?php echo e($item->sell_num); ?></td>
                                    <td><?php echo e($item->count_comment); ?></td>
                                    <td><?php echo e($item->created_at); ?></td>
                                    <td><?php echo e($item->updated_at); ?></td>
                                    <td>
                                        <?php if($item->hot == 1): ?>
                                            <i style="color: #1AB394;"><span class="fa fa-check"></span></i>
                                        <?php else: ?>
                                            <i style="color: red;"><span class="fa fa-times"></span></i>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($item->tui == 1): ?>
                                            <i style="color: #1AB394;"><span class="fa fa-check"></span></i>
                                        <?php else: ?>
                                            <i style="color: red;"><span class="fa fa-times"></span></i>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($item->xian == 1): ?>
                                            <i style="color: #1AB394;"><span class="fa fa-check"></span></i>
                                        <?php else: ?>
                                            <i style="color: red;"><span class="fa fa-times"></span></i>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($item->status == 1): ?>
                                            <i style="color: #1AB394;"><span class="fa fa-check"></span>正常</i>
                                        <?php elseif($item->status == -1): ?>
                                            <i style="color: #f8ac59;"><span class="fa fa-times"></span>下架</i>
                                        <?php else: ?>
                                            <i style="color: red;"><span class="fa fa-times"></span>删除</i>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (\Entrust::can('goods.top')) : ?>
                                        <a class="btn btn-xs <?php if($item->status == -1): ?> btn-success <?php else: ?> btn-warning <?php endif; ?> btn-outline"
                                           title="<?php if($item->status == -1): ?> 上架 <?php else: ?> 下架 <?php endif; ?>"
                                           onclick="set_status('<?php echo e($item->id); ?>', '<?php echo e($item->status); ?>')"> <span
                                                    class="<?php if($item->status == -1): ?> fa fa-arrow-circle-up <?php else: ?> fa fa-arrow-circle-down <?php endif; ?>"> </span>
                                        </a>
                                        <?php endif; // Entrust::can ?>
                                        <?php if (\Entrust::can('goods.edit')) : ?>
                                        <a class="btn btn-primary btn-outline btn-xs edit"
                                           href="<?php echo e(url('admin/goods/'.$item->id.'/edit')); ?>" title="编辑"> <span
                                                    class="fa fa-edit"></span> </a>
                                        <?php endif; // Entrust::can ?>
                                        <?php if (\Entrust::can('goods.delete')) : ?>
                                        <a onclick="del_goods('<?php echo e($item->id); ?>')"
                                           class="btn btn-danger btn-outline btn-xs edit" title="删除"> <span
                                                    class="fa fa-trash"></span> </a>
                                        <?php endif; // Entrust::can ?>																				<?php if (\Entrust::can('goods.commit')) : ?>                                        <a href="<?php echo e(url('admin/commit/'.$item->id)); ?>"                                           class="btn btn-danger btn-outline btn-xs edit" title="评价"> <span                                                    class="fa fa-comment"></span> </a>                                        <?php endif; // Entrust::can ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                            </tbody>
                        </table>
                        <div class="text-center">
                            <?php echo e($list->links()); ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function set_status(id, status) {
            layer.confirm('你确定该操作？', {
                btn: ['确定', '取消']
            }, function () {
                $.ajax({
                    type: "get",
                    data: {id: id, status: status},
                    url: '<?php echo e(url('admin/goodsTopOrDown')); ?>',
                    beforeSend: function () {
                        layer.load(0);
                    },
                    success: function (data) {
                        console.log(data);
                        if (data.status == 'success') {
                            layer.msg('操作成功！', {icon: 1, time: 800}, function () {
                                window.location.reload();
                            });
                        } else {
                            layer.alert(data.msg);
                        }
                    },
                    complete: function () {
                        //完成响应
                        layer.closeAll('loading');
                    },
                    error: function (data) {
                        layer.alert('系统异常')
                    }
                });
            });
        }
        function del_goods(id) {
            layer.confirm('你确定删除该条记录吗？', {
                btn: ['确定', '取消']
            }, function () {
                $.ajax({
                    type: "delete",
                    url: '<?php echo e(url('admin/goods')); ?>/' + id,
                    beforeSend: function () {
                        layer.load(0);
                    },
                    success: function (data) {
                        console.log(data);
                        if (data.status == 'success') {
                            layer.msg('删除成功！', {icon: 1, time: 800}, function () {
                                window.location.reload();
                            });
                        } else {
                            layer.alert(data.msg);
                        }
                    },
                    complete: function () {
                        //完成响应
                        layer.closeAll('loading');
                    },
                    error: function (data) {
                        layer.alert('系统异常')
                    }
                });
            });
        }

    </script>
    <script>
        laydate({
            elem: '#date_star',
            format: 'YYYY-MM-DD hh:mm:ss', //日期格式 // 分隔符可以任意定义，该例子表示只显示年月
            festival: true, //显示节日
            istime: true,   //是否显示时分秒
            istoday: true,
//是否是今天
            choose: function (datas) { //选择日期完毕的回调
                end.min = datas; //开始日选好后，重置结束日的最小日期
                end.start = datas //将结束日的初始值设定为开始日
            }
        });
        laydate({
            elem: '#date_end',
            format: 'YYYY-MM-DD hh:mm:ss', //日期格式 // 分隔符可以任意定义，该例子表示只显示年月
            festival: true, //显示节日
            istime: true,   //是否显示时分秒
            istoday: true,
//是否是今天
            choose: function (datas) { //选择日期完毕的回调
                end.min = datas; //开始日选好后，重置结束日的最小日期
                end.start = datas //将结束日的初始值设定为开始日
            }
        });
    </script>
    <script>
        var sort;
        $('.set-sort').blur(function () {
            var num = $(this).val();
            if (sort != num) {
                var goods_id = $(this).attr('aid');
                $.ajax({
                    type: "post",
                    data: {goods_id: goods_id, sort: num},
                    url: '<?php echo e(url('admin/set_goods_sort')); ?>',
                    beforeSend: function () {
                        layer.load(0);
                    },
                    success: function (data) {
                        console.log(data);
                        if (data.status == 'success') {
                            layer.msg('排序成功');
                        } else {
                            layer.alert(data.msg);
                        }
                    },
                    complete: function () {
                        //完成响应
                        layer.closeAll('loading');
                    },
                    error: function (data) {
                        layer.alert('系统异常')
                    }
                });
            }
        })
        $('.set-sort').focus(function () {
            sort = $(this).val();
        })
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>