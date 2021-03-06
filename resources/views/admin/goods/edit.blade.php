@extends('layouts.admin')

@section('title', '编辑商品')
@section('content')
@include('UEditor::head')
<link rel="stylesheet" type="text/css" href="{{ asset('css/plugins/webuploader/webuploader.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/plugins/webuploader/webuploader-demo.css') }}">

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>商品管理</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('admin') }}">控制台</a>
                </li>
                <li>
                    <a href="{{ url('admin/goods') }}">商品列表</a>
                </li>
                <li class="active">
                    <strong>编辑商品</strong>
                </li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="wrapper wrapper-content animated ">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <span>编辑商品</span>
                    </div>
                    <div class="ibox-content">
                        <div class="tabs-container">
                            <ul class="nav nav-tabs">
                                <li class="active"><a data-toggle="tab" href="#tab-1" aria-expanded="true">填写基本属性</a></li>
                                <li class=""><a data-toggle="tab" href="#tab-2" aria-expanded="false">上传图册</a></li>
                                <li class=""><a data-toggle="tab" href="#tab-3" aria-expanded="false">设置规格属性</a></li>
                            </ul>
                            <form class="form form-horizontal" id="form" method="post" action="{{ url('admin/goods/'.$goods_info['id'].'') }}" enctype="multipart/form-data">
                                <div class="tab-content">
                                    <div id="tab-1" class="tab-pane active">
                                        <div class="panel-body">
                                            {{--<div class="alert alert-success">--}}
                                            {{--第一步：首先填写商品的基本信息！--}}
                                            {{--</div>--}}
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">商品名称：</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" value="{{ old('name', $goods_info['name']) }}" placeholder="商品名称" id="name" name="name">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">商品描述：</label>
                                                <div class="col-sm-9">
                                                    <textarea class="form-control" name="describe" id="describe" placeholder="选填..">{{ old('describe', $goods_info['describe']) }}</textarea>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">选择分类：</label>
                                                <div class="col-sm-9">
                                                    <select name="category_id" id="category_id" class="form-control">
                                                        @foreach($category as $v)
                                                            <option value="{{ $v['id'] }}" @if(old('category_id', $goods_info['category_id']) == $v['id']) selected @endif>┠ {{ $v['category_name'] }}</option>
                                                            @if(!empty($v['child']))
                                                                @foreach($v['child'] as $_v)
                                                                    <option value="{{ $_v['id'] }}" @if(old('category_id', $goods_info['category_id']) == $_v['id']) selected @endif>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $_v['category_name'] }}</option>
                                                                    {{--@if(!empty($_v['child']))--}}
                                                                        {{--@foreach($_v['child'] as $__v)--}}
                                                                            {{--<option value="{{ $__v['id'] }}" @if(old('category_id', $goods_info['category_id']) == $__v['id']) selected @endif>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $__v['category_name'] }}</option>--}}
                                                                        {{--@endforeach--}}
                                                                    {{--@endif--}}
                                                                @endforeach
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                    <span>*请选择二级分类</span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">缩略图（建议为正方形）：</label>
                                                <div class="col-sm-2 send">
                                                    <img id="preview" src="{{ old('thumb', $goods_info['thumb']) }}">
                                                    <input type="hidden" name="thumb_hidden" id="thumb_hidden">
                                                    <input type="file" name="thumb" id="doc" onchange="javascript:setImagePreview();">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">热门：</label>
                                                <div class="col-sm-9">
                                                    <label>
                                                        <input name="hot" type="radio" id="pay-3" value="1" @if(old('hot', $goods_info['hot']) == 1) checked @endif>
                                                        是
                                                    </label> &nbsp;
                                                    <label>
                                                        <input name="hot" type="radio" id="pay-4" value="-1" @if(old('hot', $goods_info['hot']) == -1) checked @endif>
                                                        否
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">推荐：</label>
                                                <div class="col-sm-9">
                                                    <label>
                                                        <input name="tui" type="radio" id="pay-3" value="1" @if(old('tui', $goods_info['tui']) == 1) checked @endif>
                                                        是
                                                    </label> &nbsp;
                                                    <label>
                                                        <input name="tui" type="radio" id="pay-4" value="-1" @if(old('tui', $goods_info['tui']) == -1) checked @endif>
                                                        否
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">限时：</label>
                                                <div class="col-sm-9">
                                                    <label>
                                                        <input name="xian" type="radio" id="pay-3" value="1" @if(old('xian', $goods_info['xian']) == 1) checked @endif>
                                                        是
                                                    </label> &nbsp;
                                                    <label>
                                                        <input name="xian" type="radio" id="pay-4" value="-1" @if(old('xian', $goods_info['xian']) == -1) checked @endif>
                                                        否
                                                    </label>
                                                    <span style="color: #0d8ddb">(选择限时后请正确填写后面时间段)</span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label"></label>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <div class="input-group date">
                                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                            <input type="text" placeholder="开始时间" class="form-control" name="date_star" id="date_star"  value="{{ old('date_star', $goods_info['date_star']) }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group date">
                                                        <div class="input-group date">
                                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                            <input type="text" placeholder="结束时间" class="form-control " value="{{ old('date_end', $goods_info['date_end']) }}" name="date_end" id="date_end">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            {!! method_field('put') !!}
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">商品状态：</label>
                                                <div class="col-sm-9">
                                                    <label>
                                                        <input name="status" type="radio" id="pay-3" value="1" @if(old('status', $goods_info['status']) == 1) checked @endif>
                                                        上架
                                                    </label> &nbsp;
                                                    <label>
                                                        <input name="status" type="radio" id="pay-4" value="-1" @if(old('status', $goods_info['status']) == -1) checked @endif>
                                                        下架
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">排序：</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control"  placeholder="填写数字,排序越大,排名越靠前" id="sort" value="{{ old('sort', $goods_info['sort']) }}" name="sort">
                                                    <span>填写数字,排序越大,排名越靠前</span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">邮费：</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control"  placeholder="邮费" id="postage" value="{{ old('postage', $goods_info['postage']) }}" name="postage">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">商品详情：</label>
                                                <div class="col-sm-9">
                                                    <script id="container" style="height:500px;" name="content" type="text/plain">{!! old('content', $goods_info['content']) !!}</script>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="tab-2" class="tab-pane">
                                        <div class="panel-body">
                                            <div class="page-container">
                                                <h2>图册</h2>
                                                <div class="col-sm-12">
                                                    <table class="table table-hover">
                                                        <thead>
                                                        <tr>
                                                            <th>图片</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>
                                                                    @foreach($imgs as $k => $v)
                                                                        <div class="div-{{ $k }}" style="width: 150px; height: 150px; float: left; margin-right: 10px;">
                                                                            <img src="{{ $v }}" style="width: 150px; height: 150px;">
                                                                        </div>
                                                                        @endforeach
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>@foreach($imgs as $k => $v)
                                                                        <div class="text-center div-{{ $k }}" style="width: 150px; float: left; margin-right: 10px;">
                                                                            <button class="btn btn-danger del-img" aid="{{ $k }}" img="{{ $v }}" type="button">删除</button>
                                                                        </div>
                                                                    @endforeach</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div style="clear: both"></div>
                                                <hr>
                                                <h2>添加一些商品图册</h2>
                                                <p>您可以尝试文件拖拽，使用QQ截屏工具，然后激活窗口后粘贴，或者点击添加图片按钮。</p>
                                                <div id="uploader" class="wu-example">
                                                    <div class="queueList">
                                                        <div id="dndArea" class="placeholder">
                                                            <div id="filePicker" class="webuploader-container">

                                                            </div>
                                                            <p>或将照片拖到这里，单次最多可选300张</p>
                                                        </div>
                                                        <ul class="filelist"></ul></div>
                                                    <div class="statusBar" style="display:none;">
                                                        <div class="progress" style="display: none;">
                                                            <span class="text">0%</span>
                                                            <span class="percentage" style="width: 0%;"></span>
                                                        </div>
                                                        <div class="info">共0张（0B），已上传0张</div>
                                                        <div class="btns">
                                                            <div id="filePicker2" class="webuploader-container">

                                                            </div>
                                                            <div class="uploadBtn state-pedding">开始上传</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <input type="hidden" id="pic" name="pic" value="{{ old('pic', $goods_info['pic']) }}"/>
                                                <div style="clear:both;"></div>
                                            </div>

                                        </div>
                                    </div>
                                    <div id="tab-3" class="tab-pane">
                                        <div class="panel-body">
                                            <div class="col-lg-6">
                                                <label class="col-lg-12"><h3>选择规格模型：</h3></label>
                                                <div class="form-group col-lg-3">
                                                    <select name="attr_models" id="attr_models" class="form-control">
                                                        <option value="0">请选择模型...</option>
                                                        @foreach($attr_models as $item)
                                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group col-lg-3" style="margin-left: 20px;">
                                                    <button class="btn btn-success btn-outline" type="button" onclick="add_no_values()">添加无规格属性</button>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="alert alert-warning">
                                                    1. 规格模型和无属性只能选其一！<br/>
                                                    2. 添加无规格商品：请单击规格模型右侧 ‘添加无规格属性’<br/>
                                                    3. 规格填写中，除可以为空项外，任意必填项为空时，发布商品后，自动过滤本条规格！<br/>
                                                    4. <font color="red">*</font> 每个商品只能选择一种模型
                                                </div>
                                            </div>
                                            <div style="clear: both"></div>
                                            <hr>
                                            <label class="col-lg-12"><h3>属性：</h3></label>
                                            <div class="form-group col-lg-12" id="sel">
                                            </div>
                                            <div style="clear: both"></div>
                                            <hr>
                                            <label class="col-lg-12"><h3>模型属性（带 <span style="color: red; font-size: 0.8em">*</span> 为必填项）：</h3></label>
                                            <div class="form-group">
                                                <div class="col-sm-12">
                                                    <table class="table table-hover xxx">
                                                        <thead>
                                                        <tr>
                                                            <th width="10%">属性</th>
                                                            <th width="5%">货号 <a title="快速补全" onclick="set_content('goods_number')"><i class="fa fa-hand-o-down"></i></a></th>
                                                            <th width="5%"><span style="color: red">*</span> 成本价 <a onclick="set_content('chengben_price')" title="快速补全"><i class="fa fa-hand-o-down"></i></a></th>
                                                            <th width="5%"><span style="color: red">*</span> 市场价 <a onclick="set_content('market_price')" title="快速补全"><i class="fa fa-hand-o-down"></i></a></th>
                                                            <th width="5%"><span style="color: red">*</span> 售价 <a onclick="set_content('sellprice')" title="快速补全"><i class="fa fa-hand-o-down"></i></a></th>
                                                            <th width="5%"><span style="color: red">*</span> 库存 <a onclick="set_content('stock')" title="快速补全"><i class="fa fa-hand-o-down"></i></a></th>
                                                            {{--<th width="5%">图片</th>--}}
                                                            <th width="5%">操作</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody class="table table-hover" id="ttt">
                                                        @foreach($attr as $k => $v)
                                                            <tr class="div-{{ $v['id'] }}">
                                                                <td>
                                                                    @if($v['attr'] == '')
                                                                           无属性<span class="check-0"></span>
                                                                        @else

                                                                        @foreach($v['attr_array'] as $key => $value)
                                                                            <b style="color: #337ab7;">{{ $value['attr']['name'] }}：</b>{{ $value['attr_values']['value'] }}，
                                                                            @endforeach
                                                                    <span class="check-@foreach($v['attr_array'] as $key => $value){{ $value['attr_values']['value'] }}-@endforeach"></span>

                                                                    @endif
                                                                </td>
                                                                <td><input class="form-control goods_number" value="{{ $v['goods_number'] }}" name="up_attr[{{ $v['id'] }}][goods_number]" style="width: 120px; border-color: #ccc"></td>
                                                               <td><input class="form-control chengben_price" value="{{ $v['chengben_price'] }}" name="up_attr[{{ $v['id'] }}][chengben_price]" style="width: 100px; border-color: #ccc"></td>
                                                                <td><input class="form-control market_price" value="{{ $v['market_price'] }}" name="up_attr[{{ $v['id'] }}][market_price]" style="width: 100px; border-color: #ccc"></td>
                                                                <td><input class="form-control sellprice" value="{{ $v['sellprice'] }}" name="up_attr[{{ $v['id'] }}][sellprice]" style="width: 100px; border-color: #ccc"></td>
                                                                <td><input class="form-control stock" value="{{ $v['stock'] }}" name="up_attr[{{ $v['id'] }}][stock]" style="width: 100px; border-color: #ccc"></td>
                                                                <td><button class="btn btn-sm btn-outline btn-danger del_values" onclick="del_values('{{ $v['id'] }}')" title="移除" type="button"><span class="fa fa-times"></span></button></td>
                                                                </tr>
                                                            @endforeach
                                                        <tr id="tablexx"></tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{--<div id="tab-4" class="tab-pane">--}}
                                    {{--<div class="panel-body">--}}
                                    {{--<h3>选择物流信息：</h3>--}}
                                    {{--<p>--}}
                                    {{--<input type="radio"> 全国包邮--}}
                                    {{--</p>--}}
                                    {{--</div>--}}
                                    {{--</div>--}}
                                </div>
                            </form>
                        </div>
                        <br>
                        <br>
                        <br>
                        <div class="form-group">
                            <label class="col-sm-2 control-label"></label>
                            <div class="col-sm-9">
                                <button class="btn btn-primary radius add btn-block" onclick="add_goods()" type="button"><i class="fa fa-paper-plane"></i> 发布商品</button>
                            </div>
                        </div>
                        <br>
                        <br>
                        <br>
                        <br>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        // 添加全局站点信息
        var BASE_URL = '{{ asset('js/plugins/webuploader') }}';
    </script>

    <script src="{{ asset('js/plugins/webuploader/webuploader.js') }}"></script>
    <script src="{{ asset('js/plugins/webuploader/webuploader-demo.js') }}"></script>

    <script type="text/javascript">
        var ue = UE.getEditor('container');
        ue.ready(function() {
            ue.execCommand('serverparam', '_token', '{{ csrf_token() }}');//此处为支持laravel5 csrf ,根据实际情况修改,目的就是设置 _token 值.
        });
    </script>
    <script>
        var col = 1;
        //下面用于图片上传预览功能
        function setImagePreview(avalue) {
            var docObj=document.getElementById("doc");

            var imgObjPreview=document.getElementById("preview");
            if(docObj.files &&docObj.files[0])
            {
//火狐下，直接设img属性
                imgObjPreview.style.display = 'block';
                imgObjPreview.style.width = '125px';
                imgObjPreview.style.height = '125px';
//imgObjPreview.src = docObj.files[0].getAsDataURL();

//火狐7以上版本不能用上面的getAsDataURL()方式获取，需要一下方式
                imgObjPreview.src = window.URL.createObjectURL(docObj.files[0]);
                $('#thumb_hidden').val(window.URL.createObjectURL(docObj.files[0]))
            }
            else
            {
//IE下，使用滤镜
                docObj.select();
                var imgSrc = document.selection.createRange().text;
                var localImagId = document.getElementById("localImag");
//必须设置初始大小
                localImagId.style.width = "36px";
                localImagId.style.height = "36px";
//图片异常的捕捉，防止用户修改后缀来伪造图片
                try{
                    localImagId.style.filter="progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale)";
                    localImagId.filters.item("DXImageTransform.Microsoft.AlphaImageLoader").src = imgSrc;
                    $('#thumb_hidden').val(imgSrc)
                }
                catch(e)
                {
                    alert("您上传的图片格式不正确，请重新选择!");
                    return false;
                }
                imgObjPreview.style.display = 'none';
                document.selection.empty();
            }
            return true;
        }
        $('#attr_models').change(function () {
            var id = $(this).val();
            $.ajax({
                type: "get",
                url: '{{ url('admin/get_attr_models') }}/'+id,
                beforeSend: function () {
                    layer.load(0);
                },
                success: function (data) {
                    if (data.status == 'success'){
                        $('#sel').html(data.select);
                        $('#add_values').on('click', function () {
                            add_values();
                        })
                    }else {
                        layer.msg(data.msg, {icon:1, time:800});
                    }
                },
                complete: function () {
                    //完成响应
                    layer.closeAll('loading');
                    $('#sub').attr('disabled', false)
                },
                error: function (data) {
                    layer.alert('系统异常')
                }
            });
        })
        function add_values() {
            var obj = new Object();
            var i = 0;
            var _str = 'check-';
            $("#form1 select").each(function(){
                var name = $(this).attr('name');
                var value = $(this).val();
                var data = {name: name, value: value};
                obj[i] = data;
                _str = _str + $(this).find("option:selected").text()+'-';
                i++;
            });
//            alert(_str);
            var count = $('.'+_str).length;
            if (count < 1){
                $.ajax({
                    type: "post",
                    data: obj,
                    url: '{{ url('admin/get_attr_models/get_values') }}/'+col,
                    beforeSend: function () {
                        layer.load(0);
                    },
                    success: function (data) {
                        $('#tablexx').before(data.tr)
                        col = col+1;
                    },
                    complete: function () {
                        //完成响应
                        layer.closeAll('loading');
                        $('#sub').attr('disabled', false)
                    },
                    error: function (data) {
                        layer.alert('系统异常')
                    }
                });
            }else {
                layer.msg('该属性已存在！', {icon:2, time:800})
            }
        }
        function add_no_values() {
            var count = $('.check-0').length;
            if (count < 1) {
                $.ajax({
                    type: "post",
                    data: {type: 2},
                    url: '{{ url('admin/get_attr_models/get_values') }}/0',
                    beforeSend: function () {
                        layer.load(0);
                    },
                    success: function (data) {
                        $('#tablexx').before(data.tr)

                    },
                    complete: function () {
                        //完成响应
                        layer.closeAll('loading');
                        $('#sub').attr('disabled', false)
                    },
                    error: function (data) {
                        layer.alert('系统异常')
                    }
                });
            }else {
                layer.msg('该属性已存在！', {icon:2, time:800})
            }
        }
        function del_values(id) {
            layer.confirm('你确定删除该属性吗？', {
                btn: ['确定','取消']
            }, function(){
                $.ajax({
                    type: "get",
                    url: '{{ url('admin/delete_goods_attr') }}/'+id+'/{{ $goods_info['id'] }}',
                    beforeSend: function () {
                        layer.load(0);
                    },
                    success: function (data) {
                        if(data.status == 'success'){
                            layer.msg('删除成功！', {icon:1, time: 800}, function () {
                                $('.div-'+id).remove();
                            })
                        }else{
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
            })
        }
        function del_values_js(col) {
            $('.tr-'+col).remove();
        }
        function add_goods() {
            var data = $('#form').serialize();
            $.ajax({
                type: "put",
                data: data,
                url: '{{ url('admin/goods/'.$goods_info['id'].'') }}',
                beforeSend: function () {
                    layer.load(0);
                },
                success: function (data) {
                    if (data.status == 'success'){
                        $("#form").submit();
                    }else {
                        layer.alert('系统异常');
                    }
                },
                complete: function () {
                    //完成响应
                    layer.closeAll('loading');
                    $('#sub').attr('disabled', false)
                },
                error: function (data) {
                    if (data.status == '422'){
                        data = data.responseJSON;
                        var str = '';
                        for (var i in data){
                            str += data[i][0]+"， <br>";
                        }
                        if (str != ''){
                            layer.alert(str);
                            return false;
                        }
                    }
                    layer.alert('系统异常')
                }
            });
        }
        /**
         * 统一设置
         * @param name
         */
        function set_content(name) {
            var value = $('#ttt tr:first td .'+name).val();
            $('.'+name).val(value);
        }
    </script>
<script>
    $(function () {
        $('.del-img').click(function () {
            var pic = $(this).attr('img');
            var id = $(this).attr('aid');
            layer.confirm('你确定删除该张图片吗？', {
                btn: ['确定','取消']
            }, function(){
                $.ajax({
                    type: "post",
                    data: {path: pic, goods_id: '{{ $goods_info['id'] }}'},
                    url: '{{ url('admin/delete_goods_image') }}',
                    beforeSend: function () {
                        layer.load(0);
                    },
                    success: function (data) {
                        console.log(data);
                        if(data.status == 'success'){
                            layer.msg('删除成功！', {icon:1, time: 800}, function () {
                                $('.div-'+id).remove();
                                $('#pic').val(data.pic)
                            })
                        }else{
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
            })
        })
    })
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
@endsection