@extends('layouts.admin')
@section('title', '商品评论')
@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>商品管理</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('admin') }}">控制台</a>
                </li>
                <li class="active">
                    <a href="{{ url('admin/goods') }}">商品列表</a>
                </li>
                <li class="active">
                    <strong>商品评价</strong>
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
                        <table class="table table-hover ">
                            <thead>
                            <tr>
                                <th>#ID</th>
                                <th>评价内容</th>
                                <th>评价图片</th>
                                <th>评论星级</th>
                                <th>评论时间</th>
                                <th width="120">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($commit as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->content }}</td>
                                    <td>
                                        @if(!empty($item->pic))
                                            @foreach($item->pic as $v)
                                                <img src="{{ $v }}" style="width: 100px; height: 100px; border-radius: 50%">
                                            @endforeach
                                        @endif
                                    </td>
                                    <td>{{ $item->star }}</td>
                                    <td>{{ $item->created_at }}</td>
                                    <td>
                                        <a onclick="del_commit('{{ $item->id }}')" class="btn btn-danger btn-outline btn-xs edit" title="删除"><span class="fa fa-trash"></span></a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <div class="text-center">
                            {{ $commit->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function del_commit(id) {
            layer.confirm('你确定删除该条记录吗？', {
                btn: ['确定', '取消']
            }, function () {
                $.ajax({
                    type: "get",
                    data: {id: id},
                    url: '{{ url('admin/delete_commit') }}',
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
@endsection