<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="shortcut icon" href="{{ asset('img/577713.png') }}" type="image/x-icon" />
    <title> @yield('title') | 后台管理系统</title>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('font-awesome/css/font-awesome.css') }}" rel="stylesheet">
    <!-- Toastr style -->
    <link href="{{ asset('css/plugins/toastr/toastr.min.css') }}" rel="stylesheet">
    <!-- Gritter -->
    <link href="{{ asset('js/plugins/gritter/jquery.gritter.css') }}" rel="stylesheet">

    <link href="{{ asset('css/animate.css') }}" rel="stylesheet">
    {{--<link href="{{ asset('css/Animation.css') }}" rel="stylesheet">--}}
    <link href="{{ asset('css/style.css')}}" rel="stylesheet">
    <link href="{{ asset('css/nprogress.css')}}" rel="stylesheet">
    <script src="{{ asset('js/jquery-2.1.1.js') }}"></script>
    <script src="{{ asset('js/layer/laydate/laydate.js') }}"></script>
    <script src="{{ asset('js/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>
    @yield('css')
</head>
<body>
<div id="wrapper" class="pjax">
    @include('layouts.admin.sidebar')
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
                    {{--<li class="dropdown">--}}
                        {{--<a class="dropdown-toggle count-info" data-toggle="dropdown" href="{{ url('admin') }}#">--}}
                            {{--<i class="fa fa-envelope"></i>  <span class="label label-warning">16</span>--}}
                        {{--</a>--}}
                        {{--<ul class="dropdown-menu dropdown-messages">--}}
                            {{--<li>--}}
                                {{--<div class="dropdown-messages-box">--}}
                                    {{--<a href="profile.html" class="pull-left">--}}
                                        {{--<img alt="image" class="img-circle" src="{{ asset('img/a7.jpg') }}">--}}
                                    {{--</a>--}}
                                    {{--<div class="media-body">--}}
                                        {{--<small class="pull-right">46h ago</small>--}}
                                        {{--<strong>Mike Loreipsum</strong> started following <strong>Monica Smith</strong>. <br>--}}
                                        {{--<small class="text-muted">3 days ago at 7:58 pm - 10.06.2014</small>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</li>--}}
                            {{--<li class="divider"></li>--}}
                            {{--<li>--}}
                                {{--<div class="dropdown-messages-box">--}}
                                    {{--<a href="profile.html" class="pull-left">--}}
                                        {{--<img alt="image" class="img-circle" src="{{ asset('img/a4.jpg') }}">--}}
                                    {{--</a>--}}
                                    {{--<div class="media-body ">--}}
                                        {{--<small class="pull-right text-navy">5h ago</small>--}}
                                        {{--<strong>Chris Johnatan Overtunk</strong> started following <strong>Monica Smith</strong>. <br>--}}
                                        {{--<small class="text-muted">Yesterday 1:21 pm - 11.06.2014</small>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</li>--}}
                            {{--<li class="divider"></li>--}}
                            {{--<li>--}}
                                {{--<div class="dropdown-messages-box">--}}
                                    {{--<a href="profile.html" class="pull-left">--}}
                                        {{--<img alt="image" class="img-circle" src="{{ asset('img/profile.jpg') }}">--}}
                                    {{--</a>--}}
                                    {{--<div class="media-body ">--}}
                                        {{--<small class="pull-right">23h ago</small>--}}
                                        {{--<strong>Monica Smith</strong> love <strong>Kim Smith</strong>. <br>--}}
                                        {{--<small class="text-muted">2 days ago at 2:30 am - 11.06.2014</small>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</li>--}}
                            {{--<li class="divider"></li>--}}
                            {{--<li>--}}
                                {{--<div class="text-center link-block">--}}
                                    {{--<a href="mailbox.html">--}}
                                        {{--<i class="fa fa-envelope"></i> <strong>Read All Messages</strong>--}}
                                    {{--</a>--}}
                                {{--</div>--}}
                            {{--</li>--}}
                        {{--</ul>--}}
                    {{--</li>--}}
                    <li>
                        <a href="{{ url('admin/logout') }}">
                            <i class="fa fa-sign-out"></i> 退出
                        </a>
                    </li>
                </ul>

            </nav>
        </div>
        <div id="pjax-content">
            {{--fadeInRight--}}
            <div class="animated ">
                @yield('content')
            </div>
        </div>
    </div>
</div>
@if($fahuo_order_count > 0 || $shouhou_order_count > 0)
    <link href="{{ asset('css/plugins/toastr/toastr.min.css') }}" rel="stylesheet">
    <script src="{{ asset('js/plugins/toastr/toastr.min.js') }}"></script>

    <script>
        @if($fahuo_order_count > 0)
                Command: toastr['info']("<a href='{{ url('admin/order') }}'>您有 {{ $fahuo_order_count }} 笔订单需要发货！</a>")
        @endif
                @if($shouhou_order_count > 0)
                Command: toastr['info']("<a href='{{ url('admin/refund') }}'>您有 {{ $shouhou_order_count }} 笔退款退货请求！</a>")
        @endif

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
@endif
<!-- Mainly scripts -->
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/jquery.pjax.js') }}"></script>
<script src="{{ asset('js/nprogress.js') }}"></script>
<script src="{{ asset('js/layer/layer.js') }}"></script>
<script src="{{ asset('js/plugins/metisMenu/jquery.metisMenu.js') }}"></script>
<script src="{{ asset('js/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>
<!-- Custom and plugin javascript -->
<script src="{{ asset('js/inspinia.js') }}"></script>
{{--<script src="{{ asset('js/plugins/pace/pace.min.js') }}"></script>--}}
<!-- jQuery UI -->
<script src="{{ asset('js/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
<!-- GITTER -->
<script src="{{ asset('js/plugins/gritter/jquery.gritter.min.js') }}"></script>

<script src="{{ asset('js/admin-common.js') }}"></script>
@yield('js')
</body>
</html>
