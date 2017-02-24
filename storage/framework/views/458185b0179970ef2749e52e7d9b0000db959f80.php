<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="shortcut icon" href="<?php echo e(asset('img/577713.png')); ?>" type="image/x-icon" />
    <title> <?php echo $__env->yieldContent('title'); ?> | 后台管理系统</title>
    <link href="<?php echo e(asset('css/bootstrap.min.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('font-awesome/css/font-awesome.css')); ?>" rel="stylesheet">
    <!-- Toastr style -->
    <link href="<?php echo e(asset('css/plugins/toastr/toastr.min.css')); ?>" rel="stylesheet">
    <!-- Gritter -->
    <link href="<?php echo e(asset('js/plugins/gritter/jquery.gritter.css')); ?>" rel="stylesheet">

    <link href="<?php echo e(asset('css/animate.css')); ?>" rel="stylesheet">
    
    <link href="<?php echo e(asset('css/style.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('css/nprogress.css')); ?>" rel="stylesheet">
    <script src="<?php echo e(asset('js/jquery-2.1.1.js')); ?>"></script>
    <script src="<?php echo e(asset('js/layer/laydate/laydate.js')); ?>"></script>
    <script src="<?php echo e(asset('js/plugins/slimscroll/jquery.slimscroll.min.js')); ?>"></script>
    <?php echo $__env->yieldContent('css'); ?>
</head>
<body>
<div id="wrapper" class="pjax">
    <?php echo $__env->make('layouts.admin.sidebar', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <script>
        $('#xxx').slimScroll({
            width: '100%',
            height: '100%',
            alwaysVisible: true
        });
    </script>
    <div id="page-wrapper" class="gray-bg dashbard-1">
        <div class="row border-bottom">
            <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
                <div class="navbar-header">
                    <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
                </div>
                <ul class="nav navbar-top-links navbar-right">
                    
                        
                            
                        
                        
                            
                                
                                    
                                        
                                    
                                    
                                        
                                        
                                        
                                    
                                
                            
                            
                            
                                
                                    
                                        
                                    
                                    
                                        
                                        
                                        
                                    
                                
                            
                            
                            
                                
                                    
                                        
                                    
                                    
                                        
                                        
                                        
                                    
                                
                            
                            
                            
                                
                                    
                                        
                                    
                                
                            
                        
                    
                    <li>
                        <a href="<?php echo e(url('admin/logout')); ?>">
                            <i class="fa fa-sign-out"></i> 退出
                        </a>
                    </li>
                </ul>

            </nav>
        </div>
        <div id="pjax-content">
            
            <div class="animated ">
                <?php echo $__env->yieldContent('content'); ?>
            </div>
        </div>
    </div>
</div>
<?php if($fahuo_order_count > 0 || $shouhou_order_count > 0): ?>
    <link href="<?php echo e(asset('css/plugins/toastr/toastr.min.css')); ?>" rel="stylesheet">
    <script src="<?php echo e(asset('js/plugins/toastr/toastr.min.js')); ?>"></script>

    <script>
        <?php if($fahuo_order_count > 0): ?>
                Command: toastr['info']("<a href='<?php echo e(url('admin/order')); ?>'>您有 <?php echo e($fahuo_order_count); ?> 笔订单需要发货！</a>")
        <?php endif; ?>
                <?php if($shouhou_order_count > 0): ?>
                Command: toastr['info']("<a href='<?php echo e(url('admin/refund')); ?>'>您有 <?php echo e($shouhou_order_count); ?> 笔退款退货请求！</a>")
        <?php endif; ?>

                toastr.options = {
            "closeButton": true,
            "debug": true,
            "progressBar": true,
            "preventDuplicates": false,
            "positionClass": "toast-top-right",
            "onclick": null,
            "showDuration": "400",
            "hideDuration": "1000",
            "timeOut": "7000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }
    </script>
<?php endif; ?>
<!-- Mainly scripts -->
<script src="<?php echo e(asset('js/bootstrap.min.js')); ?>"></script>
<script src="<?php echo e(asset('js/jquery.pjax.js')); ?>"></script>
<script src="<?php echo e(asset('js/nprogress.js')); ?>"></script>
<script src="<?php echo e(asset('js/layer/layer.js')); ?>"></script>
<script src="<?php echo e(asset('js/plugins/metisMenu/jquery.metisMenu.js')); ?>"></script>
<script src="<?php echo e(asset('js/plugins/slimscroll/jquery.slimscroll.min.js')); ?>"></script>
<!-- Custom and plugin javascript -->
<script src="<?php echo e(asset('js/inspinia.js')); ?>"></script>

<!-- jQuery UI -->
<script src="<?php echo e(asset('js/plugins/jquery-ui/jquery-ui.min.js')); ?>"></script>
<!-- GITTER -->
<script src="<?php echo e(asset('js/plugins/gritter/jquery.gritter.min.js')); ?>"></script>

<script src="<?php echo e(asset('js/admin-common.js')); ?>"></script>
<?php echo $__env->yieldContent('js'); ?>
</body>
</html>
