
<?php $__env->startSection('title', '提现申请列表'); ?>

<?php $__env->startSection('content'); ?>
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>用户提现申请</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="<?php echo e(url('admin')); ?>">控制台</a>
                </li>
                <li class="active">
                    <strong>提现申请列表</strong>
                </li>
            </ol>
        </div>
    </div>
    <div class="wrapper wrapper-content  animated">
        
        
        
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content text-center">
                        <form role="form" action="<?php echo e(url('admin/withdraw')); ?>" class="form-inline" method="get">
                            <div class="form-group">
                                <input type="text" placeholder="用户名" name="user_name"
                                       value="<?php echo e(old('user_name', request()->get('user_name'))); ?>" class="form-control">
                            </div>
                            <div class="form-group">
                                <select class="form-control" name="status">
                                    <option value="0" <?php if(request()->get('status') == 0): ?> selected <?php endif; ?>>所有</option>
                                    <option value="-1" <?php if(request()->get('status') == -1): ?> selected <?php endif; ?>>未处理</option>
                                    <option value="1" <?php if(request()->get('status') == 1): ?> selected <?php endif; ?>>已处理</option>
                                    <option value="2" <?php if(request()->get('status') == 2): ?> selected <?php endif; ?>>已拒绝</option>
                                </select>
                            </div>
                            <button class="btn btn-success" type="submit">搜索</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <?php echo $__env->make('flash::message', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                        <table class="table table-bordered table-hover" style="font-size: 12px;">
                            <thead>
                            <tr>
                                <th>#ID</th>
                                <th>用户名</th>
                                <th>提现金额</th>
                                
                                <th>申请时间</th>
                                <th>状态</th>
                                <th width="120">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                <tr>
                                    <td><?php echo e($item->id); ?></td>
                                    <td><?php echo e($item->user_name); ?></td>
                                    <td><?php echo e($item->money); ?></td>
                                    
                                    <td><?php echo e($item->created_at); ?></td>
                                    <td id="lock_<?php echo e($item->id); ?>">
                                        <?php if($item->status == 0): ?>
                                            <i style="color: gray;"><span class="fa fa-check" title="未处理">未处理</span></i>
                                        <?php elseif($item->status == 1): ?>
                                            <i style="color: #1AB394;"><span class="fa fa-check" title="已处理">已处理</span></i>
                                        <?php else: ?>
                                            <i style="color: red;"><span class="fa fa-times" title="已拒绝">已拒绝</span></i>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (\Entrust::can('withdraw.deal')) : ?>
                                        <lock>
                                            <?php if($item->status == 0): ?>
                                                <a onclick="deal(this, <?php echo e($item->id); ?>, <?php echo e($item->status); ?>)"
                                                   class="btn btn-xs btn-warning btn-outline" title="点击处理(请在转账后使用此操作)">
                                                    <span class="fa fa-check-circle"> </span>
                                                </a>
                                                <a href="<?php echo e(url('admin/withdraw/refuse?id='.$item->id)); ?>"
                                                   class="btn btn-xs btn-warning  btn-outline" title="拒绝处理">
                                                    <span class="fa fa-times" style="color:red;"> </span>
                                                </a>
                                                <?php else: ?>
                                                <a class="btn btn-xs btn-default btn-outline disabled">
                                                    <span class="fa fa-check-circle"> </span>
                                                </a>
                                                <a class="btn btn-xs btn-default  btn-outline disabled">
                                                    <span class="fa fa-times"> </span>
                                                </a>
                                            <?php endif; ?>
                                        </lock>
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
<?php $__env->stopSection(); ?>
<script>
    function deal(v, id, status_value) {
        layer.confirm('请确认在转账完成后执行此操作',{
            btn:['确认','取消']
        } , function(){
            if (status_value == 0) {
                status_value = 1;
            }
            console.log(status_value);
            $.ajax({
                'type': 'post',
                'url': "<?php echo e(url('admin/withdraw/deal')); ?>",
                'data': {id: id, status: status_value},
                'success': function (data) {
                    console.log(data);
                    if (data == 1) {
                        layer.msg('已处理！',{icon: 1, time: 800},function(){
                            window.location.reload();
                        });
//                        $('#lock_' + id).html('<i style="color: #1AB394;"><span class="fa fa-check" title="已处理">已处理</span></i>');
//                        $(v).parents('lock').html('');
                    } else {
                        layer.msg('操作失败！');
                    }
                }
            });
        });
    }
</script>
<?php echo $__env->make('layouts.admin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>