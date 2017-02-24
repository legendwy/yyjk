
<?php $__env->startSection('title', '用户列表'); ?>
<?php $__env->startSection('content'); ?>
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-2">
            <?php if (\Entrust::can('user.agency')) : ?>
            <h2>
                <a onclick="agencySet(<?php echo e($id); ?>)" class="btn btn-xs btn-success  btn-outline">添加代理</a>
            </h2>

            <p class="font-bold  alert alert-warning m-b-sm">
                <i class="fa fa-lightbulb-o">&nbsp;用户可单独为某省或某省某市的代理</i>
            </p>
            <?php endif; // Entrust::can ?>
        </div>
    </div>
    <div class="wrapper wrapper-content  animated">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <?php echo $__env->make('flash::message', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                        <table class="table table-bordered table-hover" style="font-size: 12px;">
                            <thead>
                            <tr>
                                <th>省</th>
                                <th>市</th>
                                <th>区</th>
                                <th>代理类型</th>
                                <th>注册时间</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $__currentLoopData = $user_daili; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                <tr>
                                    <td><?php echo e($item->provinces); ?></td>
                                    <td><?php echo e($item->citys); ?></td>
                                    <td><?php echo e($item->areas); ?></td>
                                    <td>
                                        <?php if($item->daili==2): ?>区域代理
                                        <?php elseif($item->daili==3): ?> VIP代理
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e($item->created_at); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function agencySet(id) {
            parent.layer.open({
                type: 2,
                title: '添加代理',
                shadeClose: false,
                shade: 0.8,
                area: ['800px', '600px'],
                content: '<?php echo e(url('admin/agency_set')); ?>/' + id
            });
        }
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin.header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>