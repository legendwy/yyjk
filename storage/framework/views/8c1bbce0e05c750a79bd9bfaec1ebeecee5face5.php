
<?php $__env->startSection('title', '编辑权限'); ?>

<?php $__env->startSection('content'); ?>
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>权限管理</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="<?php echo e(url('admin')); ?>">控制台</a>
                </li>
                <li>
                    <a href="<?php echo e(url('admin/permission')); ?>">权限列表</a>
                </li>
                <li class="active">
                    <strong>编辑权限</strong>
                </li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="wrapper wrapper-content animated ">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <span>编辑权限</span>
                    </div>
                    <div class="ibox-content">
                        <form class="form-horizontal" id="form" action="<?php echo e(url('admin/permission/'.$permission['id'].'')); ?>" method="post">
                            <?php if(count($errors) > 0): ?>
                                <div class="alert alert-danger">
                                    <ul>
                                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                            <li><?php echo e($error); ?></li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                            <p class="font-bold  alert alert-warning m-b-sm" style="display: none" id="error"> </p>
                                <div class="form-group"><label class="col-lg-2 control-label">权限节点</label>
                                    <div class="col-lg-9"><input type="text" name="name" value="<?php echo e($permission['name']); ?>" class="form-control"><span>* 必须为 xxx.xxx 格式</span>
                                    </div>
                                </div>
                                <?php echo csrf_field(); ?>

                                <div class="form-group"><label class="col-lg-2 control-label">权限名称</label>
                                    <div class="col-lg-9"><input type="text" name="display_name"  value="<?php echo e($permission['display_name']); ?>" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group"><label class="col-lg-2 control-label">权限介绍</label>
                                    <div class="col-lg-9"><input type="text" name="description"  value="<?php echo e($permission['description']); ?>" class="form-control">
                                    </div>
                                </div>
                                <input type="hidden" name="id" value="<?php echo e($permission['id']); ?>">
                                <?php echo method_field('put'); ?>

                                <div class="form-group"><label class="col-lg-2 control-label"></label>
                                    <div class="col-lg-9">
                                        <button type="submit" class="btn btn-primary btn-block" id="sub">提交</button>
                                    </div>
                                </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>