<?php $__env->startSection('title', '分类列表'); ?>

<?php $__env->startSection('content'); ?>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>分类管理</h2>
        <ol class="breadcrumb">
            <li>
                <a href="<?php echo e(url('admin')); ?>">控制台</a>
            </li>
            <li class="active">
                <strong>分类列表</strong>
            </li>
        </ol>
    </div>
    
        
        
        
    
</div>
<div class="wrapper wrapper-content  animated">
    
        
    
    <div class="row">
        <div class="col-lg-8">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <?php echo $__env->make('flash::message', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th width="90">ID</th>
                            <th width="120">排序</th>
                            <th >展开</th>
                            <th width="200">分类名称</th>
                            <th>图标</th>
                            <th>添加时间</th>
                            <th width="120">操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                            <tr class="text-l" id="xia_<?php echo e($item['id']); ?>">
                                <td><?php echo e($item['id']); ?></td>
                                <td ><input class="form-control" style="text-align: center; width: 50px" value="<?php echo e($item['sort']); ?>"></td>
                                <td>
                                    <a class="btn btn-xs btn-warning btn-outline zhankai" id="z_<?php echo e($item['id']); ?>" zhan="0" onclick="getCate(<?php echo e($item['id']); ?>)" title="展开下级分类"><span class="fa fa-angle-double-down"></span></a>
                                </td>
                                <td>
                                    <input class="form-control" style="width: 140px;"  value="<?php echo e($item['category_name']); ?>">
                                </td>

                                <td>--</td>
                                <td><?php echo e($item['created_at']); ?></td>
                                <td>
                                    <?php if (\Entrust::can('category.edit')) : ?>
                                    <a class="btn btn-primary btn-outline btn-xs edit" href="<?php echo e(url('admin/category/'.$item['id'].'/edit')); ?>" title="编辑"> <span class="fa fa-edit"></span> </a>
                                    <?php endif; // Entrust::can ?>
                                    <?php if (\Entrust::can('category.delete')) : ?>
                                    <a  onclick="del_category('<?php echo e($item['id']); ?>')" class="btn btn-danger btn-outline btn-xs edit" title="删除"><span class="fa fa-trash"></span> </a>
                                    <?php endif; // Entrust::can ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php if (\Entrust::can('category.add')) : ?>
        <div class="col-sm-4">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>添加分类</h5>
                </div>
                <div class="ibox-content">
                    <?php if(count($errors) > 0): ?>
                        <div class="alert alert-danger">
                            <ul>
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <form class="form-horizontal" action="<?php echo e(url('admin/category')); ?>" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <div class="col-lg-12">
                                <label>上级分类：</label>
                                <select class="form-control" name="parent_id">
                                    <option value="0">顶级分类</option>
                                    <?php $__currentLoopData = $category; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                        <option value="<?php echo e($v['id']); ?>" <?php if(old('parent_id', 0) == $v['id']): ?>  selected <?php endif; ?>>┠  <?php echo e($v['category_name']); ?></option>
                                        
                                            
                                                
                                            
                                        
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-12">
                                <label>分类名称：</label>
                                <input type="text" name="category_name" placeholder="分类名称" value="<?php echo e(old('category_name')); ?>"  class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-12">
                                <label>移动端名称：</label>
                                <input type="text" name="mobile_name" placeholder="移动端名称" value="<?php echo e(old('mobile_name')); ?>"  class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-12">
                                <label>分类介绍：</label>
                                <textarea name="info" placeholder="分类介绍" class="form-control"><?php echo e(old('info')); ?></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-12">
                                <label>分类排序：</label>
                                <input type="text" name="sort" placeholder="分类排序" value="<?php echo e(old('sort', 0)); ?>"  class="form-control">
                            </div>
                        </div>
                        <div class="form-group my-icon">
                            <div class="col-lg-12">
                                <label>分类图标：</label>
                                <input type="file" name="icon" placeholder="分类图标"  class="form-control">
                                <span class="help-block m-b-none">二级分类必填</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-12">
                                <button class="btn btn-success btn-block" type="submit">添加</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php endif; // Entrust::can ?>
    </div>
</div>
    <script>
        function del_category(id) {
            layer.confirm('你确认删除此条记录吗吗？', {
                btn: ['确认','取消']
            }, function(){
                $.ajax({
                    type: "delete",
                    contentType: "application/json",
                    url: '<?php echo e(url('admin/category')); ?>/' + id,
                    beforeSend: function () {
                        layer.load(0);
                    },
                    success: function (data) {
                        if (data.status == 'success') {
                            layer.msg('删除成功', {icon: 1, time:800});
                            $('#xia_'+id).remove();
                        } else {
                            layer.msg(data.msg, {icon: 2});
                        }
                    },
                    complete: function () {//完成响应
                        layer.closeAll('loading');
                    },
                    error: function (data) {
                        console.info("error: " + data.responseText);
                    }
                })
            });
        }
        function getCate(id){
            var zhan=$("#z_"+id).attr('zhan');
            if (zhan == '0'){
                $.ajax({
                    type: "GET",
                    contentType: "application/json",
                    url: '<?php echo e(url('admin/category/getCategoryListById')); ?>/'+id,
                    beforeSend: function () {
                        layer.load(0);
                    },
                    success: function (data) {
                        if (data.status == 'success') {
                            var j = data.list;
                            var str = "";
                            for (var i = 0; i < j.length; i++) {
                                str = str + '<tr class="l_' + id + '" id="xia_'+j[i].id+'">';
                                str = str + '<td>&nbsp;&nbsp;┠&nbsp;'+j[i].id+'</td>';
                                str = str + '<td><input class="form-control" style="text-align: center; width: 50px; margin-left: 20px;" value="'+j[i].sort+'"></td>';
                                str = str + '<td><a class="btn btn-xs btn-warning btn-outline zhankai" style="margin-left: 20px;" id="z_'+j[i].id+'" zhan="0" onclick="twooGetCate('+j[i].id+', '+id+')" title="展开下级分类"><span class="fa fa-angle-double-down"></span></a></td>';
                                str = str + '<td><input class="form-control" style="margin-left: 20px; width: 140px;"  value="'+j[i].category_name+'"></td>';
                                var img = '--';
                                if (j[i].icon != ''){
                                    img = '<img src='+j[i].icon+' class="img-circle" style="width: 40px; height: 40px; border-radius: 50%">';
                                }
                                str = str + '<td style="padding-left: 20px;">'+img+'</td>';
                                str = str + '<td style="padding-left: 20px;">'+j[i].created_at+'</td>';
                                str = str + '<td><a class="btn btn-primary btn-outline btn-xs edit" href="/admin/category/'+j[i].id+'/edit" title="编辑"> <span class="fa fa-edit"></span> </a> <a onclick="del_category('+j[i].id+')" class="btn btn-danger btn-outline btn-xs edit" title="删除"><span class="fa fa-trash"></span> </a></tr>';
                            }
                            $("#xia_" + id).after(str);
                            $("#z_" + id).attr('zhan', 1);
                        }else{
                            layer.msg('无下级分类', {icon:0, time:800});
                        }
                    },
                    complete: function () {//完成响应
                        layer.closeAll('loading');
                    },
                    error: function (data) {
                        console.info("error: " + data.responseText);
                    }
                });
            }else{
                $('.l_'+id).remove();
                $("#z_" + id).attr('zhan', 0);
            }
        }
        function twooGetCate(id, id_two){

            var zhan=$("#z_"+id).attr('zhan');
            if (zhan == '0'){
                $.ajax({
                    type: "GET",
                    contentType: "application/json",
                    url: '<?php echo e(url('admin/category/getCategoryListById')); ?>/'+id,
                    beforeSend: function () {
                        layer.load(0);
                    },
                    success: function (data) {
                        if (data.status == 'success') {
                            var j = data.list;
                            var str = "";
                            for (var i = 0; i < j.length; i++) {
                                str = str + '<tr class="l_' + id_two + ' l_' + id + '" id="xia_'+j[i].id+'">';
                                str = str + '<td>&nbsp;&nbsp;&nbsp;&nbsp;┠--&nbsp;'+j[i].id+'</td>';
                                str = str + '<td><input class="form-control" style="text-align: center; width: 50px; margin-left: 50px;" value="'+j[i].sort+'"></td>';
                                str = str + '<td style="padding-left: 50px;">--</td>';
                                str = str + '<td><input class="form-control" style="margin-left: 50px; width: 140px;"  value="'+j[i].category_name+'"></td>';
                                var img = '--';
                                if (j[i].icon != ''){
                                    img = '<img src='+j[i].icon+' class="img-circle" style="width: 40px; height: 40px; border-radius: 50%">';
                                }
                                str = str + '<td style="padding-left: 50px;">'+img+'</td>';
                                str = str + '<td style="padding-left: 50px;">'+j[i].created_at+'</td>';
                                str = str + '<td><a class="btn btn-primary btn-outline btn-xs edit" href="/admin/category/'+j[i].id+'/edit" title="编辑"> <span class="fa fa-edit"></span> </a> <a onclick="del_category('+j[i].id+')" class="btn btn-danger btn-outline btn-xs edit" title="删除"><span class="fa fa-trash"></span> </a></tr>';
                            }
                            $("#xia_" + id).after(str);
                            $("#z_" + id).attr('zhan', 1);
                        }else{
                            layer.msg('无下级分类', {icon:0, time:800});
                        }
                    },
                    complete: function () {//完成响应
                        layer.closeAll('loading');
                    },
                    error: function (data) {
                        console.info("error: " + data.responseText);
                    }
                });
            }else{
                $('.l_'+id).remove();
                $("#z_" + id).attr('zhan', 0);
            }
        }
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>