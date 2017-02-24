<?php $__env->startSection('title', '订单列表'); ?>
<?php $__env->startSection('content'); ?>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12">
        <h2>退款退货原因</h2>
        <div class="col-lg-4">
            <div class="ibox-content">
                <h3>原因：</h3>
                <p><?php echo e($info->title); ?></p>
                <h3>说明：</h3>
                <p><?php echo e($info->content); ?></p>
                <?php if(!empty($imgs)): ?>
                <hr>
                <h3>图片详情</h3>
                <p>
                    <?php $__currentLoopData = $imgs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                        <div style="padding:20px; float:left;" id="photos_<?php echo e($k); ?>" class="layer-photos-demo">
                            <img width="400px" src="<?php echo e($v); ?>" >

                            <!-- <div id="photos_<?php echo e($k); ?>" class="hide layui-layer-wrap" style="display: none;"><img src="<?php echo e($v); ?>"></div> -->
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                </p>
                    <?php endif; ?>
            </div>
        </div>
    </div>
    <?php if($info->is_set == 1): ?>
    <div class="col-sm-12">
        <h2>物流信息</h2>
        <div class="ibox-content">
        <p>
        <?php if($wuliu['Success'] == true): ?>
            <p><?php if(!empty($wuliu['Reason'])): ?><?php echo e($wuliu['Reason']); ?> <?php endif; ?></p>
            <?php $__currentLoopData = $wuliu['Traces']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                <?php echo e($v['AcceptTime']); ?>：<span style="color: green"><?php echo e($v['AcceptStation']); ?></span><br/>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                <?php else: ?>
                <?php echo e($wuliu['Reason']); ?>

                <?php endif; ?>
                </p>
    </div>
        </div>
        <?php endif; ?>
</div>
    <script>
        function photo(v) {
            //页面层-佟丽娅
            // parent.layer.open({
            //     type: 1,
            //     title: false,
            //     closeBtn: 0,
            //     area: ,
            //     skin: 'layui-layer-nobg', //没有背景色
            //     shadeClose: true,
            //     content: $('#'+$(v).attr('attr_parid'))
            // });
        }
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin.header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>