@extends('layouts.admin')
@section('title', '商品列表')
@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>商品管理</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('admin') }}">控制台</a>
                </li>
                <li class="active">
                    <strong>商品列表</strong>
                </li>
            </ol>
        </div>
        <div class="col-lg-2">
            @permission(('goods.add'))
            <h2><a class="btn btn-primary btn-outline" href="{{ url('admin/goods/create') }}">添加商品</a></h2>
            @endpermission
        </div>
    </div>
    <div class="wrapper wrapper-content  animated">
        {{--<p class="font-bold  alert alert-warning m-b-sm">--}}
        {{--<i class="fa fa-lightbulb-o"></i> &nbsp;非专业人士请勿操作--}}
        {{--</p>--}}
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content text-center">
                        <form role="form" action="{{ url('admin/goods') }}" class="form-inline" method="get">
                            <div class="form-group">
                                <input type="text" placeholder="商品名称" name="name"
                                       value="{{ old('name', request()->get('name')) }}" class="form-control">
                            </div>
                            <div class="form-group">
                                <div class="input-group date">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text" placeholder="添加时间开始" class="form-control" name="date_star"
                                           id="date_star" value="{{ old('date_star', request()->get('date_star')) }}">
                                </div>
                            </div>
                            <div class="form-group date">
                                <div class="input-group date">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text" placeholder="添加时间截止" class="form-control "
                                           value="{{ old('date_end', request()->get('date_end')) }}" name="date_end"
                                           id="date_end">
                                </div>
                            </div>
                            <div class="form-group">
                                <select class="form-control" name="status">
                                    <option value="0" @if(request()->get('status') == 0) selected @endif>全部（上下架）
                                    </option>
                                    <option value="1" @if(request()->get('status') == 1) selected @endif>上架</option>
                                    <option value="-1" @if(request()->get('status') == -1) selected @endif>下架</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <select class="form-control" name="hot">
                                    <option value="0" @if(request()->get('hot') == 0) selected @endif>全部（热门）</option>
                                    <option value="1" @if(request()->get('hot') == 1) selected @endif>热门</option>
                                    <option value="-1" @if(request()->get('hot') == -1) selected @endif>非热门</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <select class="form-control" name="tui">
                                    <option value="0" @if(request()->get('tui') == 0) selected @endif>全部（推荐）</option>
                                    <option value="1" @if(request()->get('tui') == 1) selected @endif>推荐</option>
                                    <option value="-1" @if(request()->get('tui') == -1) selected @endif>非推荐</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <select class="form-control" name="xian">
                                    <option value="0" @if(request()->get('xian') == 0) selected @endif>全部（限时）</option>
                                    <option value="1" @if(request()->get('xian') == 1) selected @endif>限时</option>
                                    <option value="-1" @if(request()->get('xian') == -1) selected @endif>非限时</option>
                                </select>
                            </div>
                            <button class="btn btn-success" type="submit">搜索</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        @include('flash::message')
                        <table class="table table-hover ">
                            <thead>
                            <tr>
                                <th>#ID</th>
                                <th>排序</th>
                                <th width="380">商品名称</th>
                                <th>缩略图</th>
                                <th>分类</th>
                                <th>邮费</th>
                                <th>销售量</th>
                                <th>评论次数</th>
                                <th>添加时间</th>
                                <th>更新时间</th>
                                <th>热门</th>
                                <th>推荐</th>
                                <th>限时</th>
                                <th>状态</th>
                                <th width="120">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($list as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td><input type="text" value="{{ $item->sort }}" aid="{{ $item->id }}"
                                               class="form-control set-sort" style="width: 80px;"></td>
                                    <td>{{ $item->name }}</td>
                                    <td><img src="{{ $item->thumb }}"
                                             style="width: 35px; height: 35px; border-radius: 50%"></td>
                                    <td>{{ $item->category_name }}</td>
                                    <td>{{ $item->postage }}</td>
                                    <td>{{ $item->sell_num }}</td>
                                    <td>{{ $item->count_comment }}</td>
                                    <td>{{ $item->created_at }}</td>
                                    <td>{{ $item->updated_at }}</td>
                                    <td>
                                        @if($item->hot == 1)
                                            <i style="color: #1AB394;"><span class="fa fa-check"></span></i>
                                        @else
                                            <i style="color: red;"><span class="fa fa-times"></span></i>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->tui == 1)
                                            <i style="color: #1AB394;"><span class="fa fa-check"></span></i>
                                        @else
                                            <i style="color: red;"><span class="fa fa-times"></span></i>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->xian == 1)
                                            <i style="color: #1AB394;"><span class="fa fa-check"></span></i>
                                        @else
                                            <i style="color: red;"><span class="fa fa-times"></span></i>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->status == 1)
                                            <i style="color: #1AB394;"><span class="fa fa-check"></span>正常</i>
                                        @elseif($item->status == -1)
                                            <i style="color: #f8ac59;"><span class="fa fa-times"></span>下架</i>
                                        @else
                                            <i style="color: red;"><span class="fa fa-times"></span>删除</i>
                                        @endif
                                    </td>
                                    <td>
                                        @permission(('goods.top'))
                                        <a class="btn btn-xs @if($item->status == -1) btn-success @else btn-warning @endif btn-outline"
                                           title="@if($item->status == -1) 上架 @else 下架 @endif"
                                           onclick="set_status('{{ $item->id }}', '{{ $item->status }}')"> <span
                                                    class="@if($item->status == -1) fa fa-arrow-circle-up @else fa fa-arrow-circle-down @endif"> </span>
                                        </a>
                                        @endpermission
                                        @permission(('goods.edit'))
                                        <a class="btn btn-primary btn-outline btn-xs edit"
                                           href="{{ url('admin/goods/'.$item->id.'/edit') }}" title="编辑"> <span
                                                    class="fa fa-edit"></span> </a>
                                        @endpermission
                                        @permission(('goods.delete'))
                                        <a onclick="del_goods('{{ $item->id }}')"
                                           class="btn btn-danger btn-outline btn-xs edit" title="删除"> <span
                                                    class="fa fa-trash"></span> </a>
                                        @endpermission																				@permission(('goods.commit'))                                        <a href="{{ url('admin/commit/'.$item->id) }}"                                           class="btn btn-danger btn-outline btn-xs edit" title="评价"> <span                                                    class="fa fa-comment"></span> </a>                                        @endpermission
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <div class="text-center">
                            {{ $list->links() }}
                        </div>
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
                    url: '{{ url('admin/goodsTopOrDown') }}',
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
        function del_goods(id) {
            layer.confirm('你确定删除该条记录吗？', {
                btn: ['确定', '取消']
            }, function () {
                $.ajax({
                    type: "delete",
                    url: '{{ url('admin/goods') }}/' + id,
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
    <script>
        var sort;
        $('.set-sort').blur(function () {
            var num = $(this).val();
            if (sort != num) {
                var goods_id = $(this).attr('aid');
                $.ajax({
                    type: "post",
                    data: {goods_id: goods_id, sort: num},
                    url: '{{ url('admin/set_goods_sort') }}',
                    beforeSend: function () {
                        layer.load(0);
                    },
                    success: function (data) {
                        console.log(data);
                        if (data.status == 'success') {
                            layer.msg('排序成功');
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
            }
        })
        $('.set-sort').focus(function () {
            sort = $(this).val();
        })
    </script>
@endsection