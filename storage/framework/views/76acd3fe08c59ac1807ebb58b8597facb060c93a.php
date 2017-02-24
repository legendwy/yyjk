<?php $__env->startSection('title', '售后列表'); ?>
<?php $__env->startSection('content'); ?>
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>售后列表</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="<?php echo e(url('admin')); ?>">控制台</a>
                </li>
                <li class="active">
                    <strong>售后列表</strong>
                </li>
            </ol>
        </div>
        <div class="col-lg-2">
            <?php if (\Entrust::can('refund.add')) : ?>
            <h2><a class="btn btn-primary btn-outline" href="<?php echo e(url('admin/refund/create')); ?>">售后列表</a></h2>
            <?php endif; // Entrust::can ?>
        </div>
    </div>
    <div class="wrapper wrapper-content  animated">
        
           
        
        <div class="row"><div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content text-center">
                        <form role="form" action="<?php echo e(url('admin/refund')); ?>" class="form-inline" method="get">
                            <div class="form-group">
                                <input type="text" placeholder="编号" name="order_goods_number" value="<?php echo e(old('order_goods_number', request()->get('order_goods_number'))); ?>" class="form-control">
                            </div>
                            <div class="form-group">
                                <input type="text" placeholder="所属订单号" name="order_num" value="<?php echo e(old('order_num', request()->get('order_num'))); ?>" class="form-control">
                            </div>
                            <div class="form-group">
                                <input type="text" placeholder="购买用户" name="phone" value="<?php echo e(old('phone', request()->get('phone'))); ?>" class="form-control">
                            </div>
                            <div class="form-group">
                                <input type="text" placeholder="商品名称" name="goods_name" value="<?php echo e(old('goods_name', request()->get('goods_name'))); ?>" class="form-control">
                            </div>
                            <div class="form-group">
                                <select class="form-control" name="type">
                                    <option value="all" <?php if(empty(request()->get('type'))): ?> selected <?php endif; ?>>类型（全部）</option>
                                    <option value="1" <?php if(request()->get('type') == 1): ?> selected <?php endif; ?>>退款</option>
                                    <option value="2" <?php if(request()->get('type') == 2): ?> selected <?php endif; ?>>退货</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <select class="form-control" name="status">
                                    <option value="all" <?php if(empty(request()->get('status'))): ?> selected <?php endif; ?>>状态（全部）</option>
                                    <option value="-1" <?php if(request()->get('status') == -1): ?> selected <?php endif; ?>>待审核</option>
                                    <option value="1" <?php if(request()->get('status') == 1): ?> selected <?php endif; ?>>已拒绝</option>
                                    <option value="2" <?php if(request()->get('status') == 2): ?> selected <?php endif; ?>>退货中</option>
                                    <option value="3" <?php if(request()->get('status') == 3): ?> selected <?php endif; ?>>同意退款</option>
                                    <option value="4" <?php if(request()->get('status') == 4): ?> selected <?php endif; ?>>退货完成</option>
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
                        <table class="table  table-hover">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>编号</th>
                                <th>所属订单号</th>
                                <th>用户</th>
                                <th width="350">商品名称</th>
                                <th>商品缩略图</th>
                                <th>商品属性</th>
                                <th>退款/货原因</th>
                                <th>类型</th>
                                <th>申请时间</th>
                                <th>状态</th>
                                <th width="200">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                <tr>
                                <td><?php echo e($item->id); ?> </td>
                                <td><?php echo e($item->order_goods_number); ?> </td>
                                <td><?php echo e($item->order_num); ?> </td>
                                <td><?php echo e($item->phone); ?> </td>
                                <td><?php echo e($item->goods_name); ?> </td>
                                <td><img src="<?php echo e($item->goods_thumb); ?>" style="width: 35px; height: 35px; border-radius: 50%"> </td>
                                <td><?php echo e($item->goods_attr_values); ?> </td>
                                <td><?php echo e($item->title); ?> </td>
                                <td>
                                    <?php if($item->type == 1): ?>
                                        <span style="color: #1ab394">退款</span>
                                    <?php else: ?>
                                        <span style="color: #f8ac59">退货</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($item->time); ?> </td>
                                <td>
                                    <?php if($item->status == 0): ?>
                                        <span class="label label-warning">待审核</span>
                                    <?php elseif($item->status == 1): ?>
                                        <span class="label label-danger">拒绝退款/货</span>
                                    <?php elseif($item->status == 2): ?>
                                        <?php if($item->is_set == 1): ?>
                                            <span class="label label-primary">退货中</span>
                                            <?php else: ?>
                                            <span class="label label-warning">等待买家发货</span>
                                            <?php endif; ?>
                                    <?php elseif($item->status == 3): ?>
                                        <span class="label label-primary">同意退款</span>
                                    <?php elseif($item->status == 4): ?>
                                        <span class="label label-primary">退货完成</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a class="btn btn-xs btn-success btn-outline" onclick="view_info('<?php echo e($item->id); ?>')" title="查看退款退货理由详情"><span class="fa fa-search"></span> </a>
                                    <?php if (\Entrust::can('refund.edit')) : ?>
                                    <?php if($item->status == 0): ?>
                                        <a class="btn btn-warning btn-xs btn-outline" title="审核通过" onclick="shenhe('<?php echo e($item->id); ?>')"><span class="fa fa-check-circle-o"></span></a>
                                        <a class="btn btn-danger btn-xs btn-outline" title="拒绝审核" onclick="jujue('<?php echo e($item->id); ?>')"><span class="fa fa-times-circle-o"></span></a>
                                    <?php elseif($item->status == 2 && $item->is_set == 1): ?>
                                        <a class="btn btn-primary btn-xs btn-outline" onclick="shouhuo('<?php echo e($item->id); ?>')" title="确认收货"><span class="fa fa-check-circle-o"></span></a>
                                        <a class="btn btn-danger btn-xs btn-outline" onclick="jujue_shouhuo('<?php echo e($item->id); ?>')" title="拒绝收货"><span class="fa fa-times-circle-o"></span></a>
                                    <?php else: ?>
                                        <a class="btn btn-default btn-xs btn-outline" title="审核通过" disabled><span class="fa fa-check-circle-o"></span></a>
                                        <a class="btn btn-default btn-xs btn-outline" title="拒绝审核" disabled><span class="fa fa-times-circle-o"></span></a>
                                    <?php endif; ?>
                                    <?php endif; // Entrust::can ?>
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
        function view_info(id) {
            var index = layer.open({
                type: 2,
                title: '退款退货详情',
                shadeClose: false,
                shade: 0.8,
                area: ['60%', '90%'],
                content: '<?php echo e(url('admin/refund')); ?>/'+id
            });
            // layer.full(index);
        }
        function shenhe(id) {
            layer.confirm('你确定审核该记录吗？', {
                btn: ['确定','取消']
            }, function(){
                $.ajax({
                    type: "get",
                    url: '<?php echo e(url('admin/refund_shenhe')); ?>/'+id,
                    beforeSend: function () {
                        layer.load(0);
                    },
                    success: function (data) {
                        console.log(data);
                        if (data.status == 'success'){
                            layer.msg('审核成功！', {icon:1, time:800}, function () {
                                window.location.reload();
                            })
                        }else {
                            layer.msg(data.msg, {icon:2})
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
            })
        }
        function jujue(id) {
            layer.confirm('你确定拒绝该记录吗？', {
                btn: ['确定','取消']
            }, function(){
                $.ajax({
                    type: "get",
                    url: '<?php echo e(url('admin/refund_jujue')); ?>/'+id,
                    beforeSend: function () {
                        layer.load(0);
                    },
                    success: function (data) {
                        console.log(data);
                        if (data.status == 'success'){
                            layer.msg('拒绝成功！', {icon:1, time:800}, function () {
                                window.location.reload();
                            })
                        }else {
                            layer.msg(data.msg, {icon:2})
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
            })
        }
        function shouhuo(id) {
            layer.confirm('你确定收货吗？收货后将会把商品金额返还给用户！', {
                btn: ['确定','取消']
            }, function(){
                $.ajax({
                    type: "get",
                    url: '<?php echo e(url('admin/refund_shouhuo')); ?>/'+id,
                    beforeSend: function () {
                        layer.load(0);
                    },
                    success: function (data) {
                        console.log(data);
                        if (data.status == 'success'){
                            layer.msg('确认收货成功！', {icon:1, time:800}, function () {
                                window.location.reload();
                            })
                        }else {
                            layer.msg(data.msg, {icon:2})
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
            })
        }
        function jujue_shouhuo(id) {
            layer.confirm('你确定拒绝收货吗？', {
                btn: ['确定','取消']
            }, function(){
                $.ajax({
                    type: "get",
                    url: '<?php echo e(url('admin/refund_jujue_shouhuo')); ?>/'+id,
                    beforeSend: function () {
                        layer.load(0);
                    },
                    success: function (data) {
                        console.log(data);
                        if (data.status == 'success'){
                            layer.msg('拒绝收货成功！', {icon:1, time:800}, function () {
                                window.location.reload();
                            })
                        }else {
                            layer.msg(data.msg, {icon:2})
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
            })
        }
    </script>
    <?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>