@extends('layouts.admin.header')
@section('title', '订单列表')
@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12">
        <h2>订单详情</h2>
        <div class="col-lg-4">
            <div class="ibox-content">
                <h3>收货信息：</h3>
                <p>
                    城市：{{ $order_info->province }}，{{ $order_info->city }}，{{ $order_info->area }}<br/>
                    邮编：{{ $order_info->postcode }}<br/>
                    姓名：{{ $order_info->name }}<br/>
                    联系电话：{{ $order_info->phone }}<br/>
                    详细地址：<br/>{{ $order_info->addr }}
                </p>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="ibox-content">
                <h3>订单状态：</h3>
                @if($order_info->status == -1)
                    <span class="label label-default radius">未支付</span>
                @elseif($order_info->status == 1)
                    <span class="label label-warning radius">待发货</span>
                @elseif($order_info->status == 2)
                    <span class="label label-success radius">已发货</span>
                @elseif($order_info->status == 3)
                    <span class="label label-primary radius">已确认收货</span>
                @elseif($order_info->status == 4)
                    <span class="label label-primary radius">已完成</span>
                @elseif($order_info->status == 5)
                    <span class="label label-default radius">已取消</span>
                @elseif($order_info->status == 6)
                    <span class="label label-default radius">已关闭</span>
                @endif
                <br>
                <br>
                <h3>时间：</h3>
                下单：{{ $order_info->add_time }}<br/>
                支付：{{ $order_info->pay_time }}<br/>
                发货：{{ $order_info->fahuo_time }}<br/>
                收货：{{ $order_info->shou_time }}<br/>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="ibox-content">
                @permission(('order.edit'))
                @if($order_info->status == 1)
                    <h3>订单操作：</h3>
                    <p>
                        <button class="btn btn-outline btn-primary" onclick="fahuo('{{ $order_info['id'] }}')">发货</button>
                    </p>
                @elseif($order_info->status == -1)
                    <h3>订单操作：</h3>
                    <p>
                        <button class="btn btn-outline btn-danger" onclick="cancel_order()">取消订单</button>
                    </p>
                @elseif($order_info->status == 2)
                    <h3>发货信息：</h3>
                    <p>

                        <button class="btn btn-outline btn-danger" onclick="edit_wuliu('{{ $order_info['id'] }}')">修改物流</button>
                    </p>
                @elseif($order_info->status == 5 || $order_info->status == 6)
                    <h3>订单操作：</h3>
                    <p>
                        <button class="btn btn-outline btn-danger" onclick="del_order()">删除订单</button>
                    </p>
                @endif
                @if(!empty($order_info->remark))
                    <h3>订单备注：</h3>
                    <p>
                        {{ $order_info->remark }}
                    </p>
                @endif
                @endpermission
            </div>
        </div>
    </div>
</div>
<div class="wrapper wrapper-content  animated">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <p>订单号：{{ $order_info->order_num }}，金额：{{ $order_info->price }}，邮费：{{ $order_info->postage }}，合计：{{ $order_info->price + $order_info->postage }}</p>
                    @include('flash::message')
                    <table class="table table-hover table-bordered">
                        <thead>
                        <tr>
                            <th>#ID</th>
                            <th>编号</th>
                            <th>商品详情</th>
                            <th>单价</th>
                            <th>数量</th>
                            <th>属性</th>
                            <th>状态</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($goods_order_list as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->order_goods_number }}</td>
                            <td class="text-l">
                                <a href="#">
                                    <img src="{{ $item->goods_thumb }}" style="width: 50px; height: 50px; display: block; float: left;margin-right: 10px;">
                                    <span style="display: block;width: 300px;height:35px; float: left;"> {{ $item->goods_name }}</span>
                                </a>
                            </td>
                            <td>{{ $item->price }}</td>
                            <td>X{{ $item->num }}</td>
                            <td>{{ $item->goods_attr_values }}</td>
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
                                    <span class="label label-warning radius">申请退货</span>
                                @elseif($item->status == 7)
                                    <span class="label label-primary radius">退货中</span>
                                @elseif($item->status == 8)
                                    <span class="label label-primary radius">退货完成</span>
                                @elseif($item->status == 9)
                                    <span class="label label-warning radius">申请退款</span>
                                @elseif($item->status == 10)
                                    <span class="label label-primary radius">同意退款</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div style="clear: both"></div>
                @if($order_info->status == 2 || $order_info->status == 3 || $order_info->status == 4 || $order_info->status == 6)
                    <div class="col-sm-12">
                        <h2>物流信息</h2>
                        <hr>
                        <p>
                        @if($wuliu['Success'] == true)
                            <p>@if(!empty($wuliu['Reason'])){{ $wuliu['Reason'] }} @endif</p>
                            @foreach($wuliu['Traces'] as $k => $v)
                                {{ $v['AcceptTime'] }}：<span style="color: green">{{ $v['AcceptStation'] }}</span><br/>
                                @endforeach
                            @else
                            {{ $wuliu['Reason'] }}
                        @endif
                        </p>
                    </div>
                @endif
            </div>
            <br/>
            <br/>
            <br/>
            <br/>
            <br/>
            <br/>
            <div style="clear: both"></div>
        </div>
    </div>
</div>
    <script>
        function cancel_order() {
            layer.confirm('你确定取消该订单吗？', {
                btn: ['确定','取消']
            }, function(){
                $.ajax({
                    type: "get",
                    url: '{{ url('admin/cancel_order/'.$order_info['id'].'') }}',
                    beforeSend: function () {
                        layer.load(0);
                    },
                    success: function (data) {
                        console.log(data);
                        if (data.status == 1){
                            layer.msg('取消成功！', {icon: 1, time:800}, function () {
                                parent.window.location.reload();
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
        function del_order() {
            layer.confirm('你确定删除该条记录吗？', {
                btn: ['确定','取消']
            }, function(){
                $.ajax({
                    type: "delete",
                    url: '{{ url('admin/order/'.$order_info['id'].'') }}',
                    beforeSend: function () {
                        layer.load(0);
                    },
                    success: function (data) {
                        console.log(data);
                        if (data.status == 1){
                            layer.msg('删除成功！', {icon: 1, time:800}, function () {
                                parent.window.location.reload();
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
        function fahuo(id) {
            parent.layer.open({
                type: 2,
                title: '订单发货',
                shadeClose: false,
                shade: 0.8,
                area: ['400px', '400px'],
                content: '{{ url('admin/order_fahuo') }}/'+id
            });
        }
        function edit_wuliu(id) {
            parent.layer.open({
                type: 2,
                title: '编辑物流',
                shadeClose: false,
                shade: 0.8,
                area: ['400px', '400px'],
                content: '{{ url('admin/edit_wuliu') }}/'+id
            });
        }
    </script>
@endsection