
<?php $__env->startSection('title', '编辑管理员'); ?>

<?php $__env->startSection('content'); ?>
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>管理员管理</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="<?php echo e(url('admin')); ?>">控制台</a>
                </li>
                <li>
                    <a href="<?php echo e(url('admin/admin')); ?>">管理员列表</a>
                </li>
                <li class="active">
                    <strong>编辑管理员</strong>
                </li>
            </ol>
        </div>
    </div>
    <div class="row">
    <div class="wrapper wrapper-content animated ">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <span>编辑管理员</span>
                </div>
                <div class="ibox-content">
                    <form class="form-horizontal" id="form" action="<?php echo e(url('admin/admin/'.$admin['id'].'')); ?>" method="post">
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
                            <div class="form-group"><label class="col-lg-2 control-label">管理员名称</label>
                                <div class="col-lg-9"><input type="text" name="name"  value="<?php echo e($admin['name']); ?>" class="form-control">
                                </div>
                            </div>
                            <?php echo csrf_field(); ?>

                            <?php echo method_field('put'); ?>

                            <div class="form-group"><label class="col-lg-2 control-label">管理员邮箱</label>
                                <div class="col-lg-9"><input type="text" name="email"  value="<?php echo e($admin['email']); ?>" class="form-control">
                                </div>
                            </div>
                            <input type="hidden" name="id" value="<?php echo e($admin['id']); ?>">
                            <div class="form-group"><label class="col-lg-2 control-label">管理员密码</label>
                                <div class="col-lg-9"><input type="password" name="password" class="form-control"><span>*不修改请留空</span>
                                </div>
                            </div>
                            <?php if($admin['id'] != 1): ?>
                                <div class="form-group"><label class="col-lg-2 control-label">选择角色</label>
                                    <div class="col-lg-9">
                                        <select class="form-control" name="role_id" >
                                            <?php $__currentLoopData = $role_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                                <option value="<?php echo e($v['id']); ?>" <?php if($admin['role_id'] == $v['id']): ?> selected="selected" <?php endif; ?>><?php echo e($v['display_name']); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                                        </select>
                                    </div>
                                </div>
                                <?php endif; ?>
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