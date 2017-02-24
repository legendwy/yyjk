
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
                                <th>名字</th>
                                <th>注册时间</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $__currentLoopData = $childs_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                <tr>
                                    <td>
                                        <?php if(!$item['name']): ?>
                                            <?php echo e($item['nickname']); ?>

                                        <?php else: ?>
                                            <?php echo e($item['name']); ?>

                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php echo e($item['created_at']); ?>

                                    </td>
                                </tr>
                                <?php if(!empty($item['child'])): ?>
                                <tr>
                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <?php if(!$item['child']['name']): ?>
                                            <?php echo e($item['child']['nickname']); ?>

                                        <?php else: ?>
                                            <?php echo e($item['child']['name']); ?>

                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php echo e($item['child']['created_at']); ?>

                                    </td>
                                </tr>
                                <?php endif; ?>
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