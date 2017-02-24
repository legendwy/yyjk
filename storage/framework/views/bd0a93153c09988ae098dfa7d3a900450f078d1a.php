
<?php $__env->startSection('title', '退款退货理由列表'); ?>
<?php $__env->startSection('content'); ?>
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>退款退货理由管理</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="<?php echo e(url('admin')); ?>">控制台</a>
                </li>
                <li class="active">
                    <strong>退款退货理由列表</strong>
                </li>
            </ol>
        </div>
        <div class="col-lg-2">
            <?php if (\Entrust::can('reason.add')) : ?>
            <h2><a class="btn btn-primary btn-outline" href="<?php echo e(url('admin/reason/create')); ?>">添加退款退货理由</a></h2>
            <?php endif; // Entrust::can ?>
        </div>
    </div>
    <div class="wrapper wrapper-content  animated">
        
           
        
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <?php echo $__env->make('flash::message', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                        <table class="table  table-hover">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>理由</th>
                                <th width="120">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                <tr>
                                <td><?php echo e($item->id); ?> </td>
                                <td><?php echo e($item->title); ?> </td>
                                <td>
                                    <?php if (\Entrust::can('reason.edit')) : ?>
                                    <a href="<?php echo e(url('admin/reason/'.$item['id'].'/edit')); ?>" class="btn btn-primary btn-outline btn-xs" title="编辑"><span class="fa fa-edit"></span></a>
                                    <?php endif; // Entrust::can ?>
                                    <?php if (\Entrust::can('reason.delete')) : ?>
                                    <a onclick="del('<?php echo e($item['id']); ?>')" class="btn btn-danger btn-outline btn-xs" title="删除"><form name="delete-<?php echo e($item['id']); ?>" action="<?php echo e(url('admin/reason/'.$item['id'].'')); ?>" method="post"><?php echo csrf_field(); ?><input type="hidden" name="_method" value="delete"></form><span class="fa fa-trash"></span></a>
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
    <?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>