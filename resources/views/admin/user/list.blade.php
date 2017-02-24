@extends('layouts.admin')
@section('title', '用户列表')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>用户管理</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('admin') }}">控制台</a>
                </li>
                <li class="active">
                    <strong>用户列表</strong>
                </li>
            </ol>
        </div>
        <div class="col-lg-2">
            @permission(('user.add'))
            <h2><a class="btn btn-primary btn-outline" href="{{ url('admin/user/create') }}">添加用户</a></h2>
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
                        <form role="form" action="{{ url('admin/user') }}" class="form-inline" method="get">
                            <div class="form-group">
                                <input type="text" placeholder="用户ID" name="id"
                                       value="{{ old('id', request()->get('id')) }}" class="form-control">
                            </div>
                            <div class="form-group">
                                <input type="text" placeholder="用户名称" name="name"
                                       value="{{ old('name', request()->get('name')) }}" class="form-control">
                            </div>
                            <div class="form-group">
                                <input type="text" placeholder="电话" name="phone"
                                       value="{{ old('phone', request()->get('phone')) }}" class="form-control">
                            </div>
                            <div class="form-group">
                                <select class="form-control" name="status">
                                    <option value="0" @if(request()->get('status') == 0) selected @endif>全部</option>
                                    <option value="1" @if(request()->get('status') == 1) selected @endif>正常</option>
                                    <option value="-1" @if(request()->get('status') == -1) selected @endif>禁用</option>
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
                                <th>昵称</th>
                                <th>微信昵称</th>
                                <th>二维码</th>
                                {{--<th>邮箱</th>--}}
                                <th>手机号</th>
                                <th>云粉人数</th>
                                <th>分销收益</th>
                                <th>余额</th>
                                <th>已消费</th>
                                {{--<th>区域</th>--}}
                                <th>代理</th>
                                <th>注册时间</th>
                                <th>状态</th>
                                <th width="120">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($user_list as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->nickname }}</td>
                                    <td>
                                        @if(!empty($item->qrcode))
                                            <div id="photos_{{ $item->id }}" class="layer-photos-demo">
                                                <img height="30px" attr_parid = "photos_{{ $item->id }}" width="35px" onclick="photo(this)" layer-pid="{{ $item->id }}" layer-src="{{ $item->qrcode }}" src="{{ $item->qrcode }}" alt="{{ $item->name }}">
                                            </div>
                                        @endif
                                    </td>
                                    {{--<td>{{ $item->email }}</td>--}}
                                    <td>{{ $item->phone }}</td>
                                    <td>
                                        @permission(('user.userChild'))
                                        <a onclick="layeropen('{{ url('admin/userChild?user_id='.$item->id)}}', '下级用户', '800px', '600px')"
                                           class="btn btn-xs btn-primary  btn-outline" title="查看下级">
                                            <span class="fa fa-user-md"></span>&nbsp;{{$item->count}}人
                                        </a>
                                        @else
                                            <span class="fa fa-user-md"></span>&nbsp;{{$item->count}}人
                                            @endpermission
                                    </td>
                                    <td>
                                        @permission(('user.userFanli'))
                                        <a style="min-width: 95px;" onclick="layeropen('{{ url('admin/userFanli?user_id='.$item->id)}}', '返利记录', '800px', '600px')"
                                           class="btn btn-xs btn-success  btn-outline" title="查看返利记录">
                                            <span class="fa fa-jpy"></span>&nbsp;{{ $item->fenxiao_credit }}
                                        </a>
                                        @else
                                            <span class="fa fa-jpy"></span>&nbsp;{{ $item->fenxiao_credit }}
                                            @endpermission
                                    </td>
                                    <td>
                                        @permission(('user.userMingxi'))
                                        <a style="min-width: 95px;" onclick="layeropen('{{ url('admin/userMingxi?user_id='.$item->id)}}', '余额明细', '800px', '600px')"
                                           class="btn btn-xs btn-warning  btn-outline" title="查看余额明细">
                                            <span class="fa fa-jpy"></span>&nbsp;{{ $item->wallet }}
                                        </a>
                                        @else
                                            <span class="fa fa-jpy"></span>&nbsp;{{$item->wallet}}
                                            @endpermission

                                    </td>
                                    <td style="color: #b91c20">
                                        <span class="fa fa-jpy"></span>&nbsp;  {{$item->use_wallet}}
									</td>
                                    <td>
                                        @if($item->daili==1)否
                                        @elseif($item->daili==2)区域代理
                                        @elseif($item->daili==3) VIP代理
                                        @endif
                                    </td>
                                    <td>{{ $item->created_at }}</td>
                                    <td id="lock_{{ $item->id }}">
                                        @if($item->status == 1)
                                            <i style="color: #1AB394;"><span class="fa fa-check"></span></i>
                                        @else
                                            <i style="color: red;"><span class="fa fa-times"></span></i>
                                        @endif
                                    </td>
                                    <td>
                                        {{--@permission(('user.edit'))--}}
                                        {{--<a class="btn btn-primary btn-outline btn-xs edit" href="{{ url('admin/user/'.$item->id.'/edit') }}" title="编辑"> <span class="fa fa-edit"></span> </a>--}}
                                        {{--@endpermission--}}
                                        @permission(('user.lock'))
                                        <lock>
                                            @if($item->status == 1)
                                                <a onclick="lock(this, {{ $item->id }}, {{ $item->status }},'1')"
                                                   class="btn btn-xs btn-warning btn-outline" title="禁用账号">
                                                    <span class="fa fa-arrow-circle-down"> </span>
                                                </a>
                                            @else
                                                <a onclick="lock(this,{{ $item->id }}, {{ $item->status }},'0')"
                                                   class="btn btn-xs btn-success  btn-outline" title="取消禁用">
                                                    <span class="fa fa-arrow-circle-up "> </span>
                                                </a>
                                            @endif
                                        </lock>
                                        @endpermission
                                        @permission(('user.getUserAddress'))
                                        <a onclick="layeropen('{{ url('admin/get_user_address?user_id='.$item->id)}}', '收货地址', '1100px', '600px')"
                                           class="btn btn-xs btn-success  btn-outline" title="收货地址">
                                            <span class="fa fa-send-o"> </span>
                                        </a>
                                        @endpermission
                                        @permission(('user.agency'))
                                        <a onclick="layeropen('{{ url('admin/agency_set_list')}}/{{$item->id}}', '设置代理', '800px', '600px')"
                                           class="btn btn-xs btn-success  btn-outline" title="设置代理">
                                            <span class="fa fa-cog"></span>
                                        </a>
                                        @endpermission
                                        {{--@permission(('user.delete'))--}}
                                        {{--<a  onclick="del('{{ $item->id }}')" class="btn btn-danger btn-outline btn-xs edit" title="删除"><form name="delete-{{ $item->id }}" action="{{ url('admin/user/'.$item->id.'') }}" method="post">{!! csrf_field() !!}<input type="hidden" name="_method" value="delete"></form> <span class="fa fa-trash"></span> </a>--}}
                                        {{--@endpermission--}}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <div class="text-center">
                            {{ $user_list->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
<script>
    function photo(v) {
        console.log($(v).attr('attr_parid'));
        layer.photos({
            photos: '#' + $(v).attr('attr_parid'),
            anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
        });
    }
    function lock(v, id, status_value,flag) {
        layer.confirm('确定操作？',{
            btn:['确认','取消']
        },function(){
            if (status_value == 1) {
                status_value = 0;
            } else {
                status_value = 1;
            }
            $.ajax({
                'type': 'post',
                'url': "{{ url('admin/user/lock') }}",
                'data': {id: id, status: status_value},
                'success': function (data) {
                    console.log(data);
                    if (data == 1) {
                        layer.msg('已解冻！');
                        $('#lock_' + id).html('<i style="color: #1AB394;"><span class="fa fa-check"></span></i>');
                        $(v).parents('lock').html('<a onclick="lock(this,' + id + ', ' + status_value + ')" class="btn btn-xs btn-warning btn-outline" title="禁用账号"><span class="fa fa-arrow-circle-down"> </span></a>');
                    } else if (data == 0) {
                        $('#lock_' + id).html('<i style="color: red;"><span class="fa fa-times"></span></i>');
                        layer.msg('已禁用！');
                        $(v).parents('lock').html('<a onclick="lock(this,' + id + ', ' + status_value + ')" class="btn btn-xs btn-success  btn-outline" title="取消禁用"><span class="fa fa-arrow-circle-up"> </span></a>');
                    } else {
                        layer.msg('操作失败！');
                    }
                }
            });
        })
    }
</script>