<?php $__env->startSection('title', '用户列表'); ?>

<?php $__env->startSection('content'); ?>
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>用户管理</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="<?php echo e(url('admin')); ?>">控制台</a>
                </li>
                <li class="active">
                    <strong>用户列表</strong>
                </li>
            </ol>
        </div>
        <div class="col-lg-2">
            <?php if (\Entrust::can('user.add')) : ?>
            <h2><a class="btn btn-primary btn-outline" href="<?php echo e(url('admin/user/create')); ?>">添加用户</a></h2>
            <?php endif; // Entrust::can ?>
        </div>
    </div>
    <div class="wrapper wrapper-content  animated">
        
        
        
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content text-center">
                        <form role="form" action="<?php echo e(url('admin/user')); ?>" class="form-inline" method="get">
                            <div class="form-group">
                                <input type="text" placeholder="用户ID" name="id"
                                       value="<?php echo e(old('id', request()->get('id'))); ?>" class="form-control">
                            </div>
                            <div class="form-group">
                                <input type="text" placeholder="用户名称" name="name"
                                       value="<?php echo e(old('name', request()->get('name'))); ?>" class="form-control">
                            </div>
                            <div class="form-group">
                                <input type="text" placeholder="电话" name="phone"
                                       value="<?php echo e(old('phone', request()->get('phone'))); ?>" class="form-control">
                            </div>
                            <div class="form-group">
                                <select class="form-control" name="status">
                                    <option value="0" <?php if(request()->get('status') == 0): ?> selected <?php endif; ?>>全部</option>
                                    <option value="1" <?php if(request()->get('status') == 1): ?> selected <?php endif; ?>>正常</option>
                                    <option value="-1" <?php if(request()->get('status') == -1): ?> selected <?php endif; ?>>禁用</option>
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
                                <th>昵称</th>
                                <th>微信昵称</th>
                                <th>二维码</th>
                                
                                <th>手机号</th>
                                <th>云粉人数</th>
                                <th>分销收益</th>
                                <th>余额</th>
                                <th>已消费</th>
                                
                                <th>代理</th>
                                <th>注册时间</th>
                                <th>状态</th>
                                <th width="120">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $__currentLoopData = $user_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                <tr>
                                    <td><?php echo e($item->id); ?></td>
                                    <td><?php echo e($item->name); ?></td>
                                    <td><?php echo e($item->nickname); ?></td>
                                    <td>
                                        <?php if(!empty($item->qrcode)): ?>
                                            <div id="photos_<?php echo e($item->id); ?>" class="layer-photos-demo">
                                                <img height="30px" attr_parid = "photos_<?php echo e($item->id); ?>" width="35px" onclick="photo(this)" layer-pid="<?php echo e($item->id); ?>" layer-src="<?php echo e($item->qrcode); ?>" src="<?php echo e($item->qrcode); ?>" alt="<?php echo e($item->name); ?>">
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <td><?php echo e($item->phone); ?></td>
                                    <td>
                                        <?php if (\Entrust::can('user.userChild')) : ?>
                                        <a onclick="layeropen('<?php echo e(url('admin/userChild?user_id='.$item->id)); ?>', '下级用户', '800px', '600px')"
                                           class="btn btn-xs btn-primary  btn-outline" title="查看下级">
                                            <span class="fa fa-user-md"></span>&nbsp;<?php echo e($item->count); ?>人
                                        </a>
                                        <?php else: ?>
                                            <span class="fa fa-user-md"></span>&nbsp;<?php echo e($item->count); ?>人
                                            <?php endif; // Entrust::can ?>
                                    </td>
                                    <td>
                                        <?php if (\Entrust::can('user.userFanli')) : ?>
                                        <a style="min-width: 95px;" onclick="layeropen('<?php echo e(url('admin/userFanli?user_id='.$item->id)); ?>', '返利记录', '800px', '600px')"
                                           class="btn btn-xs btn-success  btn-outline" title="查看返利记录">
                                            <span class="fa fa-jpy"></span>&nbsp;<?php echo e($item->fenxiao_credit); ?>

                                        </a>
                                        <?php else: ?>
                                            <span class="fa fa-jpy"></span>&nbsp;<?php echo e($item->fenxiao_credit); ?>

                                            <?php endif; // Entrust::can ?>
                                    </td>
                                    <td>
                                        <?php if (\Entrust::can('user.userMingxi')) : ?>
                                        <a style="min-width: 95px;" onclick="layeropen('<?php echo e(url('admin/userMingxi?user_id='.$item->id)); ?>', '余额明细', '800px', '600px')"
                                           class="btn btn-xs btn-warning  btn-outline" title="查看余额明细">
                                            <span class="fa fa-jpy"></span>&nbsp;<?php echo e($item->wallet); ?>

                                        </a>
                                        <?php else: ?>
                                            <span class="fa fa-jpy"></span>&nbsp;<?php echo e($item->wallet); ?>

                                            <?php endif; // Entrust::can ?>

                                    </td>
                                    <td style="color: #b91c20">
                                        <span class="fa fa-jpy"></span>&nbsp;  <?php echo e($item->use_wallet); ?>

									</td>
                                    <td>
                                        <?php if($item->daili==1): ?>否
                                        <?php elseif($item->daili==2): ?>区域代理
                                        <?php elseif($item->daili==3): ?> VIP代理
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e($item->created_at); ?></td>
                                    <td id="lock_<?php echo e($item->id); ?>">
                                        <?php if($item->status == 1): ?>
                                            <i style="color: #1AB394;"><span class="fa fa-check"></span></i>
                                        <?php else: ?>
                                            <i style="color: red;"><span class="fa fa-times"></span></i>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        
                                        
                                        
                                        <?php if (\Entrust::can('user.lock')) : ?>
                                        <lock>
                                            <?php if($item->status == 1): ?>
                                                <a onclick="lock(this, <?php echo e($item->id); ?>, <?php echo e($item->status); ?>,'1')"
                                                   class="btn btn-xs btn-warning btn-outline" title="禁用账号">
                                                    <span class="fa fa-arrow-circle-down"> </span>
                                                </a>
                                            <?php else: ?>
                                                <a onclick="lock(this,<?php echo e($item->id); ?>, <?php echo e($item->status); ?>,'0')"
                                                   class="btn btn-xs btn-success  btn-outline" title="取消禁用">
                                                    <span class="fa fa-arrow-circle-up "> </span>
                                                </a>
                                            <?php endif; ?>
                                        </lock>
                                        <?php endif; // Entrust::can ?>
                                        <?php if (\Entrust::can('user.getUserAddress')) : ?>
                                        <a onclick="layeropen('<?php echo e(url('admin/get_user_address?user_id='.$item->id)); ?>', '收货地址', '1100px', '600px')"
                                           class="btn btn-xs btn-success  btn-outline" title="收货地址">
                                            <span class="fa fa-send-o"> </span>
                                        </a>
                                        <?php endif; // Entrust::can ?>
                                        <?php if (\Entrust::can('user.agency')) : ?>
                                        <a onclick="layeropen('<?php echo e(url('admin/agency_set_list')); ?>/<?php echo e($item->id); ?>', '设置代理', '800px', '600px')"
                                           class="btn btn-xs btn-success  btn-outline" title="设置代理">
                                            <span class="fa fa-cog"></span>
                                        </a>
                                        <?php endif; // Entrust::can ?>
                                        
                                        
                                        
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                            </tbody>
                        </table>
                        <div class="text-center">
                            <?php echo e($user_list->links()); ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<script>
    function photo(v) {
        console.log($(v).attr('attr_parid'));
        layer.photos({
            photos: '#' + $(v).attr('attr_parid'),
            anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
        });
    }
    function lock(v, id, status_value,flag) {
        layer.confirm('确定操作？',{
            btn:['确认','取消']
        },function(){
            if (status_value == 1) {
                status_value = 0;
            } else {
                status_value = 1;
            }
            $.ajax({
                'type': 'post',
                'url': "<?php echo e(url('admin/user/lock')); ?>",
                'data': {id: id, status: status_value},
                'success': function (data) {
                    console.log(data);
                    if (data == 1) {
                        layer.msg('已解冻！');
                        $('#lock_' + id).html('<i style="color: #1AB394;"><span class="fa fa-check"></span></i>');
                        $(v).parents('lock').html('<a onclick="lock(this,' + id + ', ' + status_value + ')" class="btn btn-xs btn-warning btn-outline" title="禁用账号"><span class="fa fa-arrow-circle-down"> </span></a>');
                    } else if (data == 0) {
                        $('#lock_' + id).html('<i style="color: red;"><span class="fa fa-times"></span></i>');
                        layer.msg('已禁用！');
                        $(v).parents('lock').html('<a onclick="lock(this,' + id + ', ' + status_value + ')" class="btn btn-xs btn-success  btn-outline" title="取消禁用"><span class="fa fa-arrow-circle-up"> </span></a>');
                    } else {
                        layer.msg('操作失败！');
                    }
                }
            });
        })
    }
</script>
<?php echo $__env->make('layouts.admin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>