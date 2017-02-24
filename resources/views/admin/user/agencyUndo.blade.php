@extends('layouts.admin.header')
@section('title', '取消代理')
@section('content')
    <div class="wrapper wrapper-content  animated ">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        @include('flash::message')
                        <table class="table table-bordered table-hover" style="font-size: 12px;">
                            <thead>
                            <tr>
                                <th>#ID</th>
                                <th>省</th>
                                <th>市</th>
                                <th>区</th>
                                <th>注册时间</th>
                                <th width="120">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($agency_list as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->province }}</td>
                                    <td>{{ $item->city }}</td>
                                    <td>{{ $item->area }}</td>
                                    <td>{{ $item->created_at }}</td>
                                    <td>
                                        @permission(('agency.undo'))
                                        <a onclick="agency({{ $item->id }},{{$item->user_id}})" id="{{$item->user_id}}"
                                           class="btn btn-xs btn-success  btn-outline" title="取消代理">
                                            <span class="fa fa-times "> </span>
                                        </a>
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
    <script src="{{ asset('js/jquery-2.1.1.js') }}"></script>
    <script type="text/javascript">
        function agency(id,user_id) {
            layer.confirm('是否取消该用户的代理',{
                btn:['确认','取消']
            },function(){
                $.ajax({
                    type: "post",
                    data: {user_id:user_id},
                    url: "{{url('admin/agency_undo')}}/" + id,
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
            })
        }
    </script>
@endsection
