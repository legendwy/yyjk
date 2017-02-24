@extends('layouts.admin.header')
@section('title', '订单列表')
@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12">
        <h2>退款退货原因</h2>
        <div class="col-lg-4">
            <div class="ibox-content">
                <h3>原因：</h3>
                <p>{{ $info->title }}</p>
                <h3>说明：</h3>
                <p>{{ $info->content }}</p>
                @if(!empty($imgs))
                <hr>
                <h3>图片详情</h3>
                <p>
                    @foreach($imgs as $k => $v)
                        <div style="padding:20px; float:left;" id="photos_{{ $k }}" class="layer-photos-demo">
                            <img width="400px" src="{{ $v }}" >

                            <!-- <div id="photos_{{ $k }}" class="hide layui-layer-wrap" style="display: none;"><img src="{{ $v }}"></div> -->
                        </div>
                        @endforeach
                </p>
                    @endif
            </div>
        </div>
    </div>
    @if($info->is_set == 1)
    <div class="col-sm-12">
        <h2>物流信息</h2>
        <div class="ibox-content">
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
        </div>
        @endif
</div>
    <script>
        function photo(v) {
            //页面层-佟丽娅
            // parent.layer.open({
            //     type: 1,
            //     title: false,
            //     closeBtn: 0,
            //     area: ,
            //     skin: 'layui-layer-nobg', //没有背景色
            //     shadeClose: true,
            //     content: $('#'+$(v).attr('attr_parid'))
            // });
        }
    </script>
@endsection