<?php $__env->startSection('title', '添加管理员'); ?>

<?php $__env->startSection('content'); ?>
    <div class="wrapper wrapper-content  animated ">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <?php echo $__env->make('flash::message', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                        <table style="border: 0" class="table table-bordered table-hover">
                            <tbody>
                            <thead>
                            <tr>
                                <th>金额</th>
                                <th>备注</th>
                                <th>状态</th>
                                <th>时间</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $__currentLoopData = $mingxi_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                <tr>
                                    <td>
                                        <?php echo e($item->money); ?>元
                                    </td>
                                    <td>
                                        <?php echo e($item->use); ?>

                                    </td>
                                    <td>
                                        <?php if($item->status == 1): ?>
                                            收入
                                            <?php else: ?>
                                            支出
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php echo e($item->created_at); ?>

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