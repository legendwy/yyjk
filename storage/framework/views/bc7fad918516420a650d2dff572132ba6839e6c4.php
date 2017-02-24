
<?php $__env->startSection('title', '订单发货'); ?>
<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="wrapper wrapper-content animated">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <form class="form-horizontal" id="form">
                        <?php if(count($errors) > 0): ?>
                            <div class="alert alert-danger">
                                <ul>
                                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                        <li><?php echo e($error); ?></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        <div class="form-group"><label class="col-lg-2 control-label">选择物流公司</label>
                            <div class="col-lg-9">
                                <select name="wuliu_gongsi" class="form-control">
                                    <?php $__currentLoopData = $wuliu; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                        <option value="<?php echo e($k->id); ?>"><?php echo e($k->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                                </select>
                            </div>
                        </div>
                        <?php echo csrf_field(); ?>

                        <div class="form-group"><label class="col-lg-2 control-label">请填写运单号</label>
                            <div class="col-lg-9">
                                <input class="form-control" name="wuliu_num" value="<?php echo e(old('wuliu_num')); ?>">
                            </div>
                        </div>
                        <div class="form-group"><label class="col-lg-2 control-label"></label>
                            <div class="col-lg-9">
                                <button type="button" class="btn btn-primary btn-block" id="sub" onclick="fahuo('<?php echo e($id); ?>')">提交</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function fahuo(id) {
        var data = $('#form').serialize();
        $.ajax({
            type: "post",
            data: data,
            url: '<?php echo e(url('admin/order_fahuo')); ?>/'+id,
            beforeSend: function () {
                layer.load(0);
            },
            success: function (data) {
                if (data.status == 'success'){
                    layer.msg('发货成功！', {icon: 1, time:800}, function () {
                        parent.window.location.reload();
                    });
                }else {
                    layer.alert(data.msg);
                }
            },
            complete: function () {
                //完成响应
                layer.closeAll('loading');
            },
            error: function (data) {
                layer.alert('系统异常')
            }
        });
    }
</script>
    <?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin.header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>