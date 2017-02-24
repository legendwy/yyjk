@extends('layouts.admin')
@section('title', '订单列表')
@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>订单管理</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ url('admin') }}">控制台</a>
            </li>
            <li class="active">
                <strong>订单列表</strong>
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
                    <form role="form" action="{{ url('admin/order') }}" id="form" class="form-inline" method="get">
                        <div class="form-group">
                            <input type="text" placeholder="订单号" name="order_num" value="{{ old('order_num', request()->get('order_num')) }}" class="form-control">
                        </div>
                        <div class="form-group">
                            <input type="text" placeholder="微信账号" name="nickname" value="{{ old('nickname', request()->get('nickname')) }}" class="form-control">
                        </div>
                        <div class="form-group">
                            <div class="input-group date">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                <input type="text" placeholder="下单时间开始" class="form-control" name="date_star" id="date_star"  value="{{ old('date_star', request()->get('date_star')) }}">
                            </div>
                        </div>
                        <div class="form-group date">
                            <div class="input-group date">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                <input type="text" placeholder="下单时间截止" class="form-control " value="{{ old('date_end', request()->get('date_end')) }}" name="date_end" id="date_end">
                            </div>
                        </div>
                        <div class="form-group">
                            <select class="form-control" name="pay_status">
                                <option value="all" @if(empty(request()->get('pay_status'))) selected @endif>支付状态（全部）</option>
                                <option value="1" @if(request()->get('pay_status') == 1) selected @endif>已支付</option>
                                <option value="-1" @if(request()->get('pay_status') == -1) selected @endif>未支付</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <select class="form-control" name="status">
                                <option value="0" @if(request()->get('status') == 0) selected @endif>订单状态（全部）</option>
                                <option value="-1" @if(request()->get('status') == -1) selected @endif>未支付</option>
                                <option value="1" @if(request()->get('status') == 1) selected @endif>待发货</option>
                                <option value="2" @if(request()->get('status') == 2) selected @endif>已发货</option>
                                <option value="3" @if(request()->get('status') == 3) selected @endif>已收货</option>
                                <option value="4" @if(request()->get('status') == 4) selected @endif>已完成</option>
                                <option value="5" @if(request()->get('status') == 5) selected @endif>已取消</option>
                                <option value="6" @if(request()->get('status') == 6) selected @endif>已关闭</option>
                            </select>
                        </div>
                        <button class="btn btn-success" type="button" onclick="search()">搜索</button>
                        &nbsp;&nbsp;&nbsp;<a onclick="exp_excel()" class="btn btn-default">导出EXCEL</a>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    @include('flash::message')
                    <table class="table table-hover  ">
                        <thead>
                        <tr>
                            <th>#ID</th>
                            <th>订单号</th>
                            <th>微信账号</th>
                            <th>商品价格</th>
                            <th>邮费</th>
                            <th>应付金额</th>
                            <th>支付状态</th>
                            <th>支付方式</th>
                            <th width="250ox;">收货信息</th>
                            <th>时间</th>
                            <th>状态</th>
                            <th width="160">操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($list as $item)
                        <tr data-toggle="collapse" data-parent="#accordion" href=".collapseOne-{{ $item->id }}" aria-expanded="true" style="cursor: pointer;" aria-controls="collapseOne-{{ $item->id }}" class="tr-{{ $item->id }}">
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->order_num }}</td>
                            <td>{{ $item->nickname }}</td>
                            <td>{{ $item->price }}</td>
                            <td>{{ $item->postage }}</td>
                            <td>{{ $item->price + $item->postage }}</td>
                            <td>
                                @if($item->pay_status == 1)
                                    <span style="color: #1AB394;"><span class="fa fa-check"></span></span>
                                @else
                                    <span style="color: red;"><span class="fa fa-times"></span></span>
                                @endif
                            </td>
                            <td>
                                @if($item->pay_status == 1)
                                    @if($item->pay_type == 1)
                                        <span style="color: #1AB394;">余额</span>
                                    @elseif($item->pay_type == 2)
                                        <span style="color: #1AB394;">微信</span>
                                    @endif
                                @else
                                    --
                                @endif
                            </td>
                            <td>
                                {{ $item->province }}，{{ $item->city }}，{{ $item->area }}
                                <div  class="panel-collapse collapse collapseOne-{{ $item->id }}" role="tabpanel" aria-labelledby="collapseOne-{{ $item->id }}">
                                    <div class="panel-body" style="background: #FFF">
                                        城市：{{ $item->province }}，{{ $item->city }}，{{ $item->area }}<br/>
                                        详细地址：{{ $item->addr }}<br/>
                                        邮编：{{ $item->postcode }}<br/>
                                        姓名：{{ $item->name }}<br/>
                                        联系电话：{{ $item->phone }}
                                    </div>
                                </div>
                            </td>
                            <td>
                                下单时间：{{ $item->add_time }}
                                <div class="panel-collapse collapse collapseOne-{{ $item->id }}" role="tabpanel" aria-labelledby="collapseOne-{{ $item->id }}">
                                    <div class="panel-body" style="background: #FFF">
                                        下单：{{ $item->add_time }}<br/>
                                        支付：{{ $item->pay_time }}<br/>
                                        发货：{{ $item->fahuo_time }}<br/>
                                        收货：{{ $item->shou_time }}<br/>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($item->status == -1)
                                    <span class="label label-default radius">未支付</span>
                                @elseif($item->status == 1)
                                    <span class="label label-warning radius">待发货</span>
                                @elseif($item->status == 2)
                                    <span class="label label-success radius">已发货</span>
                                @elseif($item->status == 3)
                                    <span class="label label-primary radius">已确认收货</span>
                                @elseif($item->status == 4)
                                    <span class="label label-primary radius">已完成</span>
                                @elseif($item->status == 5)
                                    <span class="label label-default radius">已取消</span>
                                @elseif($item->status == 6)
                                    <span class="label label-default radius">已关闭</span>
                                @endif
                            </td>
                            <td class="caozuo">
                                @permission(('order.list'))
                                <a class="btn btn-xs btn-success btn-outline" title="查看订单详情"  onclick="goods_info('{{ $item->id }}')"> <span class="fa fa-search"> </span> </a>
                                @endpermission
                                @permission(('order.edit'))
                                <a class="btn btn-xs @if($item->status == 1) btn-success btn-outline @else btn-default @endif " title="发货"  @if($item->status != 1) disabled="disabled" @else onclick="fahuo('{{ $item->id }}')" @endif> <span class="fa fa-send"> </span> </a>
                                @endpermission
                                @permission(('order.edit'))
                                <a class="btn @if($item->status != 4 && $item->status != 5 && $item->status != 6) btn-default @else btn-danger btn-outline @endif btn-xs edit" title="删除" @if($item->status != 4 && $item->status != 5 && $item->status != 6)  disabled @else  onclick="del_order('{{ $item->id }}')" @endif> <span class="fa fa-trash"></span> </a>
                                @endpermission
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
        function del_order(id) {
            layer.confirm('你确定删除该条记录吗？', {
                btn: ['确定','取消']
            }, function(){
                $.ajax({
                    type: "delete",
                    url: '{{ url('admin/order') }}/'+id,
                    beforeSend: function () {
                        layer.load(0);
                    },
                    success: function (data) {
                        console.log(data);
                        if (data.status == 1){
                            layer.msg('删除成功！', {icon: 1, time:800}, function () {
                                $('.tr-'+id).remove()
                            });
                        }else {
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
            choose: function(datas){ //选择日期完毕的回调
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
            choose: function(datas){ //选择日期完毕的回调
                end.min = datas; //开始日选好后，重置结束日的最小日期
                end.start = datas //将结束日的初始值设定为开始日
            }
        });
    </script>
    <script>
        $(".caozuo").click(function(e){
            e.stopPropagation();
        });
        function goods_info(id) {
            layer.open({
                type: 2,
                title: '订单详情',
                shadeClose: false,
                shade: 0.8,
                area: ['70%', '90%'],
                content: '{{ url('admin/order') }}/'+id
            });
        }
        function fahuo(id) {
            layer.open({
                type: 2,
                title: '订单发货',
                shadeClose: false,
                shade: 0.8,
                area: ['400px', '400px'],
                content: '{{ url('admin/order_fahuo') }}/'+id
            });
        }
        function search() {
            $('#form').attr('action', "{{ url('admin/order') }}");
            $('#form').submit();
        }
        function exp_excel() {
            $('#form').attr('action', "{{ url('admin/excel/export_order') }}");
            $('#form').submit();
        }
    </script>
@endsection