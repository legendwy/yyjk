
<?php $__env->startSection('title', '权限列表'); ?>
<?php $__env->startSection('content'); ?>
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>权限管理</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="<?php echo e(url('admin')); ?>">控制台</a>
                </li>
                <li class="active">
                    <strong>权限列表</strong>
                </li>
            </ol>
        </div>
        <div class="col-lg-2">
            <?php if (\Entrust::can('permission.add')) : ?>
            <h2><a class="btn btn-primary btn-outline" href="<?php echo e(url('admin/permission/create')); ?>">添加权限</a></h2>
            <?php endif; // Entrust::can ?>
        </div>
    </div>
    <div class="wrapper wrapper-content  animated">
        <p class="font-bold  alert alert-warning m-b-sm">
           <i class="fa fa-lightbulb-o"></i> &nbsp;非专业人士请勿操作
        </p>
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <?php echo $__env->make('flash::message', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>权限节点</th>
                                <th>名称</th>
                                <th>介绍</th>
                                <th width="120">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                <tr>
                                <td><?php echo e($item->id); ?> </td>
                                <td><?php echo e($item->name); ?> </td>
                                <td><?php echo e($item->display_name); ?></td>
                                <td><?php echo e($item->description); ?></td>
                                <td>
                                    <?php if (\Entrust::can('permission.edit')) : ?>
                                    <a href="<?php echo e(url('admin/permission/'.$item['id'].'/edit')); ?>" class="btn btn-primary btn-outline btn-xs" title="编辑"><span class="fa fa-edit"></span></a>
                                    <?php endif; // Entrust::can ?>
                                    <?php if (\Entrust::can('permission.delete')) : ?>
                                    <a onclick="del('<?php echo e($item['id']); ?>')" class="btn btn-danger btn-outline btn-xs" title="删除"><form name="delete-<?php echo e($item['id']); ?>" action="<?php echo e(url('admin/permission/'.$item['id'].'')); ?>" method="post"><?php echo csrf_field(); ?><input type="hidden" name="_method" value="delete"></form><span class="fa fa-trash"></span></a>
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