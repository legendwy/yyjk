@extends('layouts.admin')
@section('title', '提现申请列表')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>用户提现申请</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('admin') }}">控制台</a>
                </li>
                <li class="active">
                    <strong>提现申请列表</strong>
                </li>
            </ol>
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
                        <form role="form" action="{{ url('admin/withdraw') }}" class="form-inline" method="get">
                            <div class="form-group">
                                <input type="text" placeholder="用户名" name="user_name"
                                       value="{{ old('user_name', request()->get('user_name')) }}" class="form-control">
                            </div>
                            <div class="form-group">
                                <select class="form-control" name="status">
                                    <option value="0" @if(request()->get('status') == 0) selected @endif>所有</option>
                                    <option value="-1" @if(request()->get('status') == -1) selected @endif>未处理</option>
                                    <option value="1" @if(request()->get('status') == 1) selected @endif>已处理</option>
                                    <option value="2" @if(request()->get('status') == 2) selected @endif>已拒绝</option>
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
                        <table class="table table-bordered table-hover" style="font-size: 12px;">
                            <thead>
                            <tr>
                                <th>#ID</th>
                                <th>用户名</th>
                                <th>提现金额</th>
                                
                                <th>申请时间</th>
                                <th>状态</th>
                                <th width="120">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->user_name }}</td>
                                    <td>{{ $item->money }}</td>
                                    
                                    <td>{{ $item->created_at }}</td>
                                    <td id="lock_{{ $item->id }}">
                                        @if($item->status == 0)
                                            <i style="color: gray;"><span class="fa fa-check" title="未处理">未处理</span></i>
                                        @elseif($item->status == 1)
                                            <i style="color: #1AB394;"><span class="fa fa-check" title="已处理">已处理</span></i>
                                        @else
                                            <i style="color: red;"><span class="fa fa-times" title="已拒绝">已拒绝</span></i>
                                        @endif
                                    </td>
                                    <td>
                                        @permission(('withdraw.deal'))
                                        <lock>
                                            @if($item->status == 0)
                                                <a onclick="deal(this, {{ $item->id }}, {{ $item->status }})"
                                                   class="btn btn-xs btn-warning btn-outline" title="点击处理(请在转账后使用此操作)">
                                                    <span class="fa fa-check-circle"> </span>
                                                </a>
                                                <a href="{{ url('admin/withdraw/refuse?id='.$item->id)}}"
                                                   class="btn btn-xs btn-warning  btn-outline" title="拒绝处理">
                                                    <span class="fa fa-times" style="color:red;"> </span>
                                                </a>
                                                @else
                                                <a class="btn btn-xs btn-default btn-outline disabled">
                                                    <span class="fa fa-check-circle"> </span>
                                                </a>
                                                <a class="btn btn-xs btn-default  btn-outline disabled">
                                                    <span class="fa fa-times"> </span>
                                                </a>
                                            @endif
                                        </lock>
                                        @endpermission
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <div class="text-center">
                            {{--                            {{ $user_list->links() }}--}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
<script>
    function deal(v, id, status_value) {
        layer.confirm('请确认在转账完成后执行此操作',{
            btn:['确认','取消']
        } , function(){
            if (status_value == 0) {
                status_value = 1;
            }
            console.log(status_value);
            $.ajax({
                'type': 'post',
                'url': "{{ url('admin/withdraw/deal') }}",
                'data': {id: id, status: status_value},
                'success': function (data) {
                    console.log(data);
                    if (data == 1) {
                        layer.msg('已处理！',{icon: 1, time: 800},function(){
                            window.location.reload();
                        });
//                        $('#lock_' + id).html('<i style="color: #1AB394;"><span class="fa fa-check" title="已处理">已处理</span></i>');
//                        $(v).parents('lock').html('');
                    } else {
                        layer.msg('操作失败！');
                    }
                }
            });
        });
    }
</script>