@extends('layouts.admin')
@section('title', '意见反馈')
@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>商品管理</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('admin') }}">控制台</a>
                </li>
                <li class="active">
                    <strong>意见反馈</strong>
                </li>
            </ol>
        </div>
    </div>
    <div class="wrapper wrapper-content  animated">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        @include('flash::message')
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>#ID</th>
                                <th>联系方式</th>
                                <th>反馈内容</th>
                                <th>反馈时间</th>
                                {{--<th>更新时间</th>--}}
                                <th width="120">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->tel }}</td>
                                    <td style="width: 50%;">{{ $item->content }}</td>
                                    <td>{{ $item->created_at }}</td>
{{--                                    <td>{{ $item->updated_at }}</td>--}}
                                    <td>
                                        @permission(('feedback.deal'))
                                        @if($item->status == 0)
                                            <a class="btn btn-xs btn-success btn-outline" title="点击处理"
                                               onclick="set_status('{{ $item->id }}', '{{ $item->status }}')"> <span
                                                        class="@if($item->status == 0) fa fa-arrow-circle-up @else fa fa-arrow-circle-down @endif"> </span>
                                            </a>
                                        @else
                                            <a class="btn btn-xs btn-default btn-outline disabled">
                                                <span class="fa fa-check-circle"> </span>
                                            </a>
                                        @endif
                                        @endpermission
                                        @permission(('feedback.delete'))
                                        <a onclick="del_feedback('{{ $item->id }}')"
                                           class="btn btn-danger btn-outline btn-xs edit" title="删除"> <span
                                                    class="fa fa-trash"></span> </a>
                                        @endpermission
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function set_status(id, status) {
            layer.confirm('你确定该操作？', {
                btn: ['确定', '取消']
            }, function () {
                $.ajax({
                    type: "get",
                    data: {id: id, status: status},
                    url: '{{ url('admin/feedbackDeal') }}',
                    beforeSend: function () {
                        layer.load(0);
                    },
                    success: function (data) {
                        console.log(data);
                        if (data.status == 'success') {
                            layer.msg('操作成功！', {icon: 1, time: 800}, function () {
                                window.location.reload();
                            });
                        } else {
                            layer.alert(data.msg);
                        }
                    },
                    complete: function () {
                        //完成响应
                        layer.closeAll('loading');
                    },
                    error: function (data) {
                        layer.alert('系统异常')
                    }
                });
            });
        }
        function del_feedback(id) {
            layer.confirm('你确定删除该条记录吗？', {
                btn: ['确定', '取消']
            }, function () {
                $.ajax({
                    type: "delete",
                    url: '{{ url('admin/feedback') }}/' + id,
                    beforeSend: function () {
                        layer.load(0);
                    },
                    success: function (data) {
                        console.log(data);
                        if (data.status == 'success') {
                            layer.msg('删除成功！', {icon: 1, time: 800}, function () {
                                window.location.reload();
                            });
                        } else {
                            layer.alert(data.msg);
                        }
                    },
                    complete: function () {
                        //完成响应
                        layer.closeAll('loading');
                    },
                    error: function (data) {
                        layer.alert('系统异常')
                    }
                });
            });
        }

    </script>
    <script>
        laydate({
            elem: '#date_star',
            format: 'YYYY-MM-DD hh:mm:ss', //日期格式 // 分隔符可以任意定义，该例子表示只显示年月
            festival: true, //显示节日
            istime: true,   //是否显示时分秒
            istoday: true,
//是否是今天
            choose: function (datas) { //选择日期完毕的回调
                end.min = datas; //开始日选好后，重置结束日的最小日期
                end.start = datas //将结束日的初始值设定为开始日
            }
        });
        laydate({
            elem: '#date_end',
            format: 'YYYY-MM-DD hh:mm:ss', //日期格式 // 分隔符可以任意定义，该例子表示只显示年月
            festival: true, //显示节日
            istime: true,   //是否显示时分秒
            istoday: true,
//是否是今天
            choose: function (datas) { //选择日期完毕的回调
                end.min = datas; //开始日选好后，重置结束日的最小日期
                end.start = datas //将结束日的初始值设定为开始日
            }
        });
    </script>
@endsection