
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
                    <strong>广告列表</strong>
                </li>
            </ol>
        </div>
        <div class="col-lg-2">
            <?php if (\Entrust::can('guang.add')) : ?>
            <h2><a class="btn btn-primary btn-outline" href="<?php echo e(url('admin/guang/create/?position_id='.$id)); ?>">添加轮播图</a></h2>
            <?php endif; // Entrust::can ?>
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
                                <th>#ID</th>
                                <th>所属广告位</th>
                                <th>图片</th>
                                <th>路径</th>
                                <th>图片尺寸</th>
                                <th>创建时间</th>
                                <th>排序</th>
                                <th width="120">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                <tr>
                                    <td><?php echo e($item->id); ?></td>
                                    <td><?php echo e($item->name); ?></td>
                                    <td><img src="<?php echo e($item->image); ?>" style="width:100px;height: 50px;"/></td>
                                    <td><a href="<?php echo e($item->url); ?>" target="_blank"><?php echo e($item->url); ?></a></td>
                                    <td><?php echo e($item->size); ?></td>
                                    <td><?php echo e($item->created_at); ?></td>
                                    <td><?php echo e($item->sort); ?></td>
                                    <td>
                                        <?php if (\Entrust::can('guang.edit')) : ?>
                                        <a class="btn btn-primary btn-outline btn-xs edit" href="<?php echo e(url('admin/guang/'.$item->id.'/edit')); ?>" title="编辑"> <span class="fa fa-edit"></span> </a>
                                        <?php endif; // Entrust::can ?>
                                        <?php if (\Entrust::can('guang.delete')) : ?>
                                        <a  onclick="del('<?php echo e($item['id']); ?>')" class="btn btn-danger btn-outline btn-xs edit" title="删除"><form name="delete-<?php echo e($item->id); ?>" action="<?php echo e(url('admin/guang/'.$item->id.'')); ?>" method="post"><?php echo csrf_field(); ?><input type="hidden" name="_method" value="delete"></form> <span class="fa fa-trash"></span> </a>
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