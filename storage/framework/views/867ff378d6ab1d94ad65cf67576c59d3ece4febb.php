
<?php $__env->startSection('title', '设置代理'); ?>
<?php $__env->startSection('content'); ?>
    <div class="wrapper wrapper-content  animated ">
        <div class="row">
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
                            <p class="font-bold  alert alert-warning m-b-sm" style="display: none" id="error"></p>
                            <?php echo csrf_field(); ?>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">*区域选择</label>

                                <div class="col-sm-10">
                                    <select class="form-control m-b" id="province" name="province">
                                        <option value="">请选择</option>
                                        <?php $__currentLoopData = $province; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                            <option value="<?php echo e($p->REGION_ID); ?>" ><?php echo e($p->REGION_NAME); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                                    </select>
                                    <select class="form-control m-b" class="form-control" id="city" name="city">
                                        <option value="">请选择</option>
                                    </select>
                                    <select class="form-control m-b" class="form-control" id="area" name="area">
                                        <option value="">请选择</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">*代理类型</label>
                                <div class="col-sm-10">
                                    <label>
                                        <input type="radio" name="daili" value="2" />区域代理
                                    </label>
                                    <label>
                                        <input type="radio" name="daili" value="3"  />VIP代理
                                    </label>
                                </div>
                            </div>
                            <div class="form-group"><label class="col-lg-2 control-label"></label>

                                <div class="col-lg-9">
                                    <button type="button" class="btn btn-primary btn-block" id="sub"
                                            onclick="agency('<?php echo e($id); ?>')">提交
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="<?php echo e(asset('js/jquery-2.1.1.js')); ?>"></script>
    <script type="text/javascript">
        $(function () {
            $('#province').change(function () {

                var p_id = $(this).val();
                if (p_id) {
                    $('#city').html('');
                    $('#area').html('<option value="">请选择</option>');
                }
//            console.log(p_id);
                $.ajax({
                    'type': 'post',
                    'url': "<?php echo e(url('admin/area')); ?>",
                    'data': {pid: p_id},
                    'success': function (data) {
//                        console.log(data);
                        if (data.status == 1) {
                            var html = '<option value="">请选择</option>';
                            $.each(data.data, function (i, n) {
                                html += '<option value="' + n.REGION_ID + '">' + n.REGION_NAME + '</option>'
                            })
                            $('#city').append(html);
                        }
                    }
                });
            })
            $('#city').change(function () {

                var p_id = $(this).val();
                if (p_id) {
                    $('#area').html('');
                }
//            console.log(p_id);
                $.ajax({
                    'type': 'post',
                    'url': "<?php echo e(url('admin/area')); ?>",
                    'data': {pid: p_id},
                    'success': function (data) {
//                        console.log(data);
                        if (data.status == 1) {

                            var html = '<option value="">请选择</option>';
                            $.each(data.data, function (i, n) {
                                html += '<option value="' + n.REGION_ID + '">' + n.REGION_NAME + '</option>'
                            })
                            $('#area').append(html);
                        }
                    }
                });
            })
        })
        function agency(user_id) {
            var data = $('#form').serialize();
            $.ajax({
                type: "post",
                data: data,
                url: "<?php echo e(url('admin/agency_set')); ?>/" + user_id,
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
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin.header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>