
<?php $__env->startSection('title', '取消代理'); ?>
<?php $__env->startSection('content'); ?>
    <div class="wrapper wrapper-content  animated ">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <?php echo $__env->make('flash::message', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                        <table class="table table-bordered table-hover" style="font-size: 12px;">
                            <thead>
                            <tr>
                                <th>#ID</th>
                                <th>省</th>
                                <th>市</th>
                                <th>区</th>
                                <th>注册时间</th>
                                <th width="120">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $__currentLoopData = $agency_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                <tr>
                                    <td><?php echo e($item->id); ?></td>
                                    <td><?php echo e($item->province); ?></td>
                                    <td><?php echo e($item->city); ?></td>
                                    <td><?php echo e($item->area); ?></td>
                                    <td><?php echo e($item->created_at); ?></td>
                                    <td>
                                        <?php if (\Entrust::can('agency.undo')) : ?>
                                        <a onclick="agency(<?php echo e($item->id); ?>,<?php echo e($item->user_id); ?>)" id="<?php echo e($item->user_id); ?>"
                                           class="btn btn-xs btn-success  btn-outline" title="取消代理">
                                            <span class="fa fa-times "> </span>
                                        </a>
                                        <?php endif; // Entrust::can ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                            </tbody>
                        </table>
                        <div class="text-center">
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="<?php echo e(asset('js/jquery-2.1.1.js')); ?>"></script>
    <script type="text/javascript">
        function agency(id,user_id) {
            layer.confirm('是否取消该用户的代理',{
                btn:['确认','取消']
            },function(){
                $.ajax({
                    type: "post",
                    data: {user_id:user_id},
                    url: "<?php echo e(url('admin/agency_undo')); ?>/" + id,
                    beforeSend: function () {
                        layer.load(0);
                    },
                    success: function (data) {
                        console.log(data);
                        if (data.status == 1) {
                            layer.msg(data.msg, {icon: 1, time: 800}, function () {
                                parent.window.location.reload();
                            })
                        } else {
                            layer.alert(data.msg);
                        }
                    },
                    complete: function () {
                        //完成响应
                        layer.closeAll('loading');
                    },
                    error: function () {
                        layer.alert('系统异常');
                    }
                })
            })
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin.header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>