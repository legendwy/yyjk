
<?php $__env->startSection('title', '拒绝理由填写'); ?>
<?php $__env->startSection('content'); ?>
    <div class="wrapper wrapper-content animated ">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <span>拒绝理由</span>
                </div>
                <div class="ibox-content">
                    <form class="form-horizontal" id="form" action="<?php echo e(url('admin/withdraw/refuse_reason')); ?>" method="post">
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
                            
                            <?php echo csrf_field(); ?>

                        <div class="form-group"><label class="col-lg-2 control-label">*拒绝理由</label>
                            <div class="col-lg-9"><textarea name="reason" cols="" rows="" class="form-control"></textarea>
                            </div>
                        </div>
                            <input type="hidden" name="id" value="<?php echo e($id); ?>" />
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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>