<?php $__env->startSection('title', '控制台'); ?>

<style type="text/css">

    ${demo.css}

</style>

<?php $__env->startSection('content'); ?>

<div class="row wrapper border-bottom white-bg page-heading">

    <div class="col-lg-10">

        <h2>欢迎你！<?php echo e(Auth::guard('admin')->user()->name); ?></h2>

    </div>

</div>

<div class="wrapper wrapper-content  animated">

    <div class="row">

        <div class="col-lg-3">

            <div class="widget style1 yellow-bg">

                <div class="row">

                    <div class="col-xs-4">

                        <i class="fa fa-user fa-5x"></i>

                    </div>

                    <div class="col-xs-8 text-right">

                        <span> 会员数量 </span>

                        <h2 class="font-bold"><?php echo e($count['user']); ?></h2>

                    </div>

                </div>

            </div>

        </div>

        <div class="col-lg-3">

            <div class="widget style1 navy-bg">

                <div class="row">

                    <div class="col-xs-4">

                        <i class="fa fa-tags fa-5x"></i>

                    </div>

                    <div class="col-xs-8 text-right">

                        <span> 订单数量 </span>

                        <h2 class="font-bold"><?php echo e($count['order']); ?></h2>

                    </div>

                </div>

            </div>

        </div>

        <div class="col-lg-3">

            <div class="widget style1 lazur-bg">

                <div class="row">

                    <div class="col-xs-4">

                        <i class="fa fa-shopping-cart fa-5x"></i>

                    </div>

                    <div class="col-xs-8 text-right">

                        <span> 商品数量 </span>

                        <h2 class="font-bold"><?php echo e($count['goods']); ?></h2>

                    </div>

                </div>

            </div>

        </div>

        <div class="col-lg-3">

            <div class="widget style1 blue-bg">

                <div class="row">

                    <div class="col-xs-4">

                        <i class="fa fa-wechat fa-5x"></i>

                    </div>

                    <div class="col-xs-8 text-right">

                        <span> 评论数量 </span>

                        <h2 class="font-bold"><?php echo e($count['comment']); ?></h2>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <div class="row  border-bottom white-bg dashboard-header">



        <div class="col-sm-3">

            <h2>新会员</h2>

            <small>最近6条数据</small>

            <ul class="list-group clear-list m-t">

                <?php $__currentLoopData = $user_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k =>  $item): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>

                    <li class="list-group-item fist-item">

                                <span class="pull-right">

                                   <?php echo e($item->created_at); ?>


                                </span>

                        <span class="label label-primary"><?php echo e($k + 1); ?></span> <?php echo e($item->nickname); ?>


                    </li>

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>

            </ul>

        </div>

        <div class="col-sm-9">

            <div id="container" style="min-width: 310px; height: 320px; margin: 0 auto"></div>

        </div>

    </div>

</div>

<script type="text/javascript">

    $(function () {

        var date = new Array();

        var sum = new Array();

        var pay = new Array();

        $.ajax({

            type: "get",

            url: '<?php echo e(url('admin/get_order_count')); ?>',

            beforeSend: function () {

                layer.load(0);

            },

            success: function (data) {

                for(var i in data){

                    date[i] = data[i].click_date;

                    sum[i] = data[i].count;

                    pay[i] = data[i].pay;

                }

                Highcharts.chart('container', {

                    title: {

                        text: '订单走势',

                        x: -20 //center

                    },

                    credits: {

                        enabled: false

                    },

                    xAxis: {

                        categories: date

                    },

                    yAxis: {

                        title: {

                            text: '交易数量'

                        },

                        plotLines: [{

                            value: 0,

                            width: 1,

                            color: '#808080'

                        }]

                    },

                    tooltip: {

                        crosshairs: true,

                        shared: true,

                        valueSuffix: '笔'

                    },



                    legend: {

                        layout: 'vertical',

                        align: 'right',

                        verticalAlign: 'middle',

                        borderWidth: 0

                    },

                    series: [{

                        name: '订单量',

                        data: sum

                    }, {

                        name: '已支付',

                        data: pay

                    }]

                });

            },

            complete: function () {

                //完成响应

                layer.closeAll('loading');

            }

        });

    });

</script>

<script src="<?php echo e(asset('js/plugins/Highcharts/highcharts.js')); ?>"></script>

<script src="<?php echo e(asset('js/plugins/Highcharts/exporting.js')); ?>"></script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>