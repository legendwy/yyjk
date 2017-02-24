
<?php $__env->startSection('title', '编辑规格模型'); ?>
<style>
    .xxx input{
        border-color: #C9C5C5;
    }
</style>
<?php $__env->startSection('content'); ?>
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>规格模型管理</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="<?php echo e(url('admin')); ?>">控制台</a>
                </li>
                <li>
                    <a href="<?php echo e(url('admin/attr_models')); ?>">规格模型列表</a>
                </li>
                <li class="active">
                    <strong>编辑规格模型</strong>
                </li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="wrapper wrapper-content animated ">
            <div class="col-lg-12">
                <div class="alert alert-warning">
                    修改模型不影响原有商品属性！
                </div>
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <span>编辑规格模型</span>
                    </div>
                    <div class="ibox-content">
                        <form class="form form-horizontal" id="add" method="post">
                            <div class="form-group">
                                <label class="col-sm-1 control-label">模型名：</label>
                                <div class="col-sm-9">
                                    <input name="name" value="<?php echo e($attr_models->name); ?>" type="text" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-1 control-label">模型属性：</label>
                                <div class="col-sm-11">
                                    <button class="btn btn-outline btn-success" type="button" onclick="addAttr()">添加属性</button>
                                    <table class="table table-hover xxx">
                                        <thead>
                                        <tr>
                                            <th width="12%">属性</th>
                                            <th width="5%">操作</th>
                                            <th width="85%">属性值</th>
                                        </tr>
                                        </thead>
                                        <tbody class="table table-hover" id="ttt">
                                        <?php $__currentLoopData = $attr_arr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                            <tr style="margin-top: 20px;" class="tr_old_<?php echo e($k); ?>">
                                                <td><input class="form-control" style="width: 90px; float: left" value="<?php echo e($v['attr']->name); ?>" name="data_update[attr][<?php echo e($v['attr']->id); ?>]"><a onclick="del_attr_old('<?php echo e($k); ?>', '<?php echo e($v['attr']->id); ?>')" style="float: left; color: red"><span class="fa fa-times"></span></a></td>
                                                <td><button class="btn btn-warning btn-outline add-attr" type="button" onclick="add_attr_value('<?php echo e($k); ?>', '<?php echo e($v['attr']->id); ?>')" style="float: left; margin-right: 10px;">添加</button></td>
                                                <td>
                                                    <?php $__currentLoopData = $v['attr_values']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $_k => $_v): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                                        <div class="attr_value_old_<?php echo e($_v->id); ?>"><input class="form-control" value="<?php echo e($_v->value); ?>" style="width: 70px; float: left; margin-left: 15px;" name="data_update[attrValue][<?php echo e($_v->id); ?>]"><a onclick="del_attr_value('<?php echo e($_v->id); ?>', '<?php echo e($_v->id); ?>')" style="float: left; color: red"><span class="fa fa-times"></span></a></div>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                                                    <span class="td_up_<?php echo e($k); ?>"></span>
                                                </td>
                                            </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                                        <tr id="tablexx"></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"></label>
                                <div class="col-sm-9">
                                    <button class="btn btn-success btn-block"  type="button" id="sub" onclick="subAttr()" >提交更新数据</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        var id = 1;
        //添加属性值
        function add_attr_value(id, attr_id) {
            layer.prompt({title: '请输入新的属性值', formType: 0}, function(attr_value, index){
                $.ajax({
                    type: "post",
                    data: {attr_models_id: '<?php echo e($attr_models->id); ?>', attr_id: attr_id, attr_value: attr_value},
                    url: '<?php echo e(url('admin/edit_attr_models/add_attr_value')); ?>',
                    beforeSend: function () {
                        layer.load(0);
                    },
                    success: function (data) {
                        if (data.status == 'success'){
                            layer.close(index);
                            layer.msg('添加成功！', {icon:1, time:800}, function () {
                                var td = '<div><input class="form-control" value="'+attr_value+'" style="width: 70px; float: left; margin-left: 15px;" name="data_update[attrValue]['+data.id+']"><a style="float: left; color: red"><span class="fa fa-times"></span></a></div>';
                                $('.td_up_'+id).before(td)
                            });
                        }else {
                            layer.msg('添加失败！', {icon:0});
                        }
                    },
                    complete: function () {
                        layer.closeAll('loading');
                    },
                    error: function (data) {
                        layer.alert('系统异常')
                    }
                });
            });
        }
        //删除老属性
        function del_attr_old(id, attr_id) {
            layer.confirm('你确认删除吗？', {
                btn: ['确认','取消']
            }, function(){
                $.ajax({
                    type: "post",
                    data: {attr_models_id: '<?php echo e($attr_models->id); ?>', attr_id: attr_id},
                    url: '<?php echo e(url('admin/edit_attr_models/delete_attr')); ?>',
                    beforeSend: function () {
                        layer.load(0);
                    },
                    success: function (data) {
                        if (data.status == 'success'){
                            layer.msg('删除成功！', {icon:1, time:800}, function () {
                                $('.tr_old_'+id).remove()
                            });
                        }else {
                            layer.msg(data.msg, {icon:0});
                        }
                    },
                    complete: function () {
                        layer.closeAll('loading');
                    },
                    error: function (data) {
                        layer.alert('系统异常')
                    }
                });
            });
        }
        //删除老属性值
        function del_attr_value(id, attr_value_id) {
            layer.confirm('你确认删除吗？', {
                btn: ['确认','取消']
            }, function(){
                $.ajax({
                    type: "post",
                    data: {attr_models_id: '<?php echo e($attr_models->id); ?>', attr_value_id: attr_value_id},
                    url: '<?php echo e(url('admin/edit_attr_models/delete_attr_value')); ?>',
                    beforeSend: function () {
                        layer.load(0);
                    },
                    success: function (data) {
                        if (data.status == 'success'){
                            layer.msg('删除成功！', {icon:1, time:800}, function () {
                                $('.attr_value_old_'+attr_value_id).remove()
                            });
                        }else {
                            layer.msg(data.msg, {icon:0});
                        }
                    },
                    complete: function () {
                        layer.closeAll('loading');
                    },
                    error: function (data) {
                        layer.alert('系统异常')
                    }
                });
            });
        }
        //添加新属性
        function addAttr() {
            var str = '<tr style="margin-top: 20px;" class="tr_'+id+'">' +
                    '<td><input class="form-control" style="width: 90px; float: left" name="data['+id+'][attr]"><a onclick="del_attr('+id+')" style="float: left; color: red"><span class="fa fa-times"></span></a></td>' +
                    '<td><button class="btn btn-warning btn-outline add-attr" type="button" onclick="add_attr('+id+')" style="float: left; margin-right: 10px;">添加</button></td>' +
                    '<td class="aaa_'+id+'"><div class="_td_0"> <input class="form-control" style="width: 70px; float: left; margin-left: 15px;" name="data['+id+'][attrValue][]"><a onclick="del_attr_value_add('+id+', 0)" style="float: left; color: red"><span class="fa fa-times"></span></a></div> <span class="td_'+id+'"></span></td>' +
                    '</tr>';
            $('#tablexx').before(str);
            id = id + 1;
        }
        //添加新属性值
        function add_attr(id) {
            var num = $(".aaa_"+id+" div").length;
            var td = '<div class="_td_'+num+'"><input class="form-control" style="width: 70px; float: left; margin-left: 15px;" name="data['+id+'][attrValue][]"><a onclick="del_attr_value_add('+id+', '+num+')" style="float: left; color: red"><span class="fa fa-times"></span></a></div>';
            $('.td_'+id).before(td);
        }
        /**
         * 删除新加属性
         * @param  id
         */
        function del_attr(id) {
            $('.tr_'+id).remove();
        }
        /**
         * 删除新加属性值
         */
        function del_attr_value_add(id, num) {
            $('.aaa_'+id+' ._td_'+num).remove()
        }
        function subAttr() {
            var data = $('#add').serialize();
            $.ajax({
                type: "put",
                data: data,
                url: '<?php echo e(url('admin/attr_models/'.$attr_models->id.'')); ?>',
                beforeSend: function () {
                    layer.load(0);
                    $('#sub').attr('disabled', true)
                },
                success: function (data) {
                    if (data.status == 'success'){
                        layer.msg('更新成功！', {icon:1, time:800}, function () {
                            window.location.href="<?php echo e(url('admin/attr_models')); ?>"
                        });
                    }else {
                        layer.msg('更新失败！', {icon:0});
                    }
                },
                complete: function () {//完成响应
                    layer.closeAll('loading');
                    $('#sub').attr('disabled', false)
                },
                error: function (data) {
                    data = data.responseJSON;
                    var str = '';
                    for (var i in data){
                        str += data[i][0]+'， ';
                    }
                    if (str != ''){
                        layer.alert(str)
                    }else {
                        layer.alert('系统异常')
                    }
                }
            });
        }
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>