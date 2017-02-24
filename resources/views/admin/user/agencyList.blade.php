@extends('layouts.admin.header')
@section('title', '用户列表')
@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-2">
            @permission(('user.agency'))
            <h2>
                <a onclick="agencySet({{$id}})" class="btn btn-xs btn-success  btn-outline">添加代理</a>
            </h2>

            <p class="font-bold  alert alert-warning m-b-sm">
                <i class="fa fa-lightbulb-o">&nbsp;用户可单独为某省或某省某市的代理</i>
            </p>
            @endpermission
        </div>
    </div>
    <div class="wrapper wrapper-content  animated">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        @include('flash::message')
                        <table class="table table-bordered table-hover" style="font-size: 12px;">
                            <thead>
                            <tr>
                                <th>省</th>
                                <th>市</th>
                                <th>区</th>
                                <th>代理类型</th>
                                <th>注册时间</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($user_daili as $item)
                                <tr>
                                    <td>{{$item->provinces}}</td>
                                    <td>{{$item->citys}}</td>
                                    <td>{{$item->areas}}</td>
                                    <td>
                                        @if($item->daili==2)区域代理
                                        @elseif($item->daili==3) VIP代理
                                        @endif
                                    </td>
                                    <td>{{ $item->created_at }}</td>
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
        function agencySet(id) {
            parent.layer.open({
                type: 2,
                title: '添加代理',
                shadeClose: false,
                shade: 0.8,
                area: ['800px', '600px'],
                content: '{{ url('admin/agency_set') }}/' + id
            });
        }
    </script>
@endsection