
<?php $__env->startSection('title', '控制台'); ?>

<?php $__env->startSection('content'); ?>
    <?php $menus = app('App\Repositories\Presenter\MenuPresenter'); ?>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>菜单管理</h2>
        <ol class="breadcrumb">
            <li>
                <a href="<?php echo e(url('admin')); ?>">控制台</a>
            </li>
            <li class="active">
                <strong>菜单列表</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2">
        <?php if (\Entrust::can('menu.add')) : ?>
        <h2><a class="btn btn-primary btn-outline" href="<?php echo e(url('admin/menu/create')); ?>">添加菜单</a></h2>
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
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>菜单名</th>
                            <th>链接</th>
                            <th>高亮</th>
                            <th>权限</th>
                            <th>排序</th>
                            <th width="120">操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                        <tr>
                            <td><span class="<?php echo e($item['icon']); ?>"></span> &nbsp;<?php echo e($item['name']); ?> </td>
                            <td><?php echo e($item['url']); ?></td>
                            <td><?php echo e($item['heightlight_url']); ?></td>
                            <td><?php echo e($item['slug']); ?></td>
                            <td><?php echo e($item['sort']); ?></td>
                            <td>
                                <?php if (\Entrust::can('menu.edit')) : ?>
                                <a class="btn btn-primary btn-outline btn-xs edit" href="<?php echo e(url('admin/menu/'.$item['id'].'/edit')); ?>" title="编辑"><span class="fa fa-edit"></span></a>
                                <?php endif; // Entrust::can ?>
                                <?php if (\Entrust::can('menu.delete')) : ?>
                                <a  onclick="del('<?php echo e($item['id']); ?>')" class="btn btn-danger btn-outline btn-xs edit" title="删除"><form name="delete-<?php echo e($item['id']); ?>" action="<?php echo e(url('admin/menu/'.$item['id'].'')); ?>" method="post"><?php echo csrf_field(); ?><input type="hidden" name="_method" value="delete"></form><span class="fa fa-trash"></span></a>
                                <?php endif; // Entrust::can ?>
                            </td>
                        </tr>
                            <?php $__currentLoopData = $item['child']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $_item): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                            <tr>
                                <td>  &nbsp; ┠  &nbsp; <?php echo e($_item['name']); ?> </td>
                                <td><?php echo e($_item['url']); ?></td>
                                <td><?php echo e($_item['heightlight_url']); ?></td>
                                <td><?php echo e($_item['slug']); ?></td>
                                <td><?php echo e($_item['sort']); ?></td>
                                <td>
                                    <?php if (\Entrust::can('menu.edit')) : ?>
                                    <a class="btn btn-primary btn-outline btn-xs edit" href="<?php echo e(url('admin/menu/'.$_item['id'].'/edit')); ?>" title="编辑"><span class="fa fa-edit"></span></a>
                                    <?php endif; // Entrust::can ?>
                                    <?php if (\Entrust::can('menu.delete')) : ?>
                                    <a  onclick="del('<?php echo e($_item['id']); ?>')" class="btn btn-danger btn-outline btn-xs edit" title="删除"><form name="delete-<?php echo e($_item['id']); ?>" action="<?php echo e(url('admin/menu/'.$_item['id'].'')); ?>" method="post"><?php echo csrf_field(); ?><input type="hidden" name="_method" value="delete"></form><span class="fa fa-trash"></span> </a>
                                    <?php endif; // Entrust::can ?>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
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