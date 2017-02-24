
<?php $__env->startSection('title', '广告列表'); ?>

<?php $__env->startSection('content'); ?>
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>广告管理</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="<?php echo e(url('admin')); ?>">控制台</a>
                </li>
                <li class="active">
                    <strong>欢迎图列表</strong>
                </li>
            </ol>
        </div>
    </div>
    <div class="wrapper wrapper-content  animated">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <?php echo $__env->make('flash::message', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>图片</th>
                                <th>尺寸</th>
                                <th>创建时间</th>
                                <th width="120">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                <tr>
                                    <td><img src="<?php echo e($item['image']); ?>" style="width:100px;height: 150px;"/></td>
                                    <td><?php echo e($item['size']); ?></td>
                                    <td><?php echo e($item['created_at']); ?></td>
                                    <td>
                                        <?php if (\Entrust::can('guang.edit')) : ?>
                                        <a class="btn btn-primary btn-outline btn-xs edit" href="<?php echo e(url('admin/image/'.$item['id'].'/edit')); ?>" title="编辑"> <span class="fa fa-edit"></span> </a>
                                        <?php endif; // Entrust::can ?>

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
<?php echo $__env->make('layouts.admin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>