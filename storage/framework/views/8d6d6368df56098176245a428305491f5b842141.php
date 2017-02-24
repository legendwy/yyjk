
<?php $__env->startSection('title', '添加管理员'); ?>

<?php $__env->startSection('content'); ?>
<div class="wrapper wrapper-content  animated ">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <?php echo $__env->make('flash::message', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>省</th>
                            <th>市</th>
                            <th>区</th>
                            <th>街道信息</th>
                            <th>详细地址</th>
                            <th>收货人姓名</th>
                            <th>手机号码</th>
                            <th>默认地址</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $__currentLoopData = $address_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                            <tr>
                                <td><?php echo e($item->province); ?> </td>
                                <td><?php echo e($item->city); ?></td>
                                <td><?php echo e($item->area); ?></td>
                                <td><?php echo e($item->street); ?></td>
                                <td><?php echo e($item->address); ?></td>
                                <td><?php echo e($item->name); ?></td>
                                <td><?php echo e($item->phone); ?></td>
                                <td>
                                    <?php if($item->status == 1): ?>
                                        是
                                    <?php else: ?>
                                        否
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin.header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>