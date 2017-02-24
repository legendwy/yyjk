<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> <?php echo $__env->yieldContent('title'); ?> | 后台管理系统</title>
    <link href="<?php echo e(asset('css/bootstrap.min.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('font-awesome/css/font-awesome.css')); ?>" rel="stylesheet">
    <!-- Toastr style -->
    <link href="<?php echo e(asset('css/plugins/toastr/toastr.min.css')); ?>" rel="stylesheet">
    <!-- Gritter -->
    <link href="<?php echo e(asset('js/plugins/gritter/jquery.gritter.css')); ?>" rel="stylesheet">

    <link href="<?php echo e(asset('css/animate.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('css/style.css')); ?>" rel="stylesheet">
    <?php echo $__env->yieldContent('css'); ?>
</head>
<body>
<div id="wrapper gray-bg dashbard-1">
    
        <?php echo $__env->yieldContent('content'); ?>
    
</div>
<!-- Mainly scripts -->
<script src="<?php echo e(asset('js/jquery-2.1.1.js')); ?>"></script>
<script src="<?php echo e(asset('js/bootstrap.min.js')); ?>"></script>
<script src="<?php echo e(asset('js/layer/layer.js')); ?>"></script>
<script src="<?php echo e(asset('js/plugins/metisMenu/jquery.metisMenu.js')); ?>"></script>
<script src="<?php echo e(asset('js/plugins/slimscroll/jquery.slimscroll.min.js')); ?>"></script>
<!-- Custom and plugin javascript -->
<script src="<?php echo e(asset('js/inspinia.js')); ?>"></script>

<!-- jQuery UI -->
<script src="<?php echo e(asset('js/plugins/jquery-ui/jquery-ui.min.js')); ?>"></script>
<!-- GITTER -->
<script src="<?php echo e(asset('js/plugins/gritter/jquery.gritter.min.js')); ?>"></script>
<script src="<?php echo e(asset('js/plugins/slimscroll/jquery.slimscroll.min.js')); ?>"></script>
<?php echo $__env->yieldContent('js'); ?>
</body>
</html>
