@extends('layouts.admin')
@section('title', '系统配置')
<link rel="stylesheet" type="text/css" href="{{ asset('css/plugins/webuploader/webuploader.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/plugins/webuploader/webuploader-demo.css') }}">
<style>
    .send {
        display: block;
        height: 105px;
        width: 135px;
        /*border-radius: 20%;*/
        overflow: hidden;
        position: relative;
        margin: 10px auto;
        margin-left: 18px;
        padding: 0px;
        /*-moz-box-shadow: 0px 0px 10px #999;*/
        /*-webkit-box-shadow: 0px 0px 10px #999;*/
        /*box-shadow: 0px 0px 10px #999;*/
    }
    .send input{
        display: block;
        width: 100%;
        height: 100%;
        position: absolute;
        left: 0;
        top: 0;
        cursor: pointer;
        z-index: 55;
        opacity: 0;
    }
    .send img{
        display: block;
        width: 135px;
        height: 105px;
        overflow: hidden;
        /*border-radius: 20%;*/
    }
</style>
@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>系统配置</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('admin') }}">控制台</a>
                </li>
                <li class="active">
                    <strong>系统配置</strong>
                </li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="wrapper wrapper-content animated ">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <span>系统配置</span>
                    </div>
                    <div class="ibox-content">
                        <div class="tabs-container">
                            @include('flash::message')
                            <ul class="nav nav-tabs">
                                <li class="active"><a data-toggle="tab" href="#tab-1">网站基本配置</a></li>
                                <li class=""><a data-toggle="tab" href="#tab-2">比例配置</a></li>
                                <li class=""><a data-toggle="tab" href="#tab-3">其他配置</a></li>
                            </ul>
                            <form class="form form-horizontal" id="update" method="post" action="{{ url('admin/sys/updateSys') }}" enctype="multipart/form-data">
                                {!! csrf_field() !!}
                            <div class="tab-content">
                                <div id="tab-1" class="tab-pane active">
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="col-lg-2 control-label">{{ $config['1']['remark'] }}</label>
                                            <div class="col-lg-9"><input type="text" name="{{ $config['1']['id'] }}" class="form-control" value="{{ $config['1']['value'] }}">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-lg-2 control-label">{{ $config['2']['remark'] }}</label>
                                            <div class="col-lg-9">
                                                <textarea name="{{ $config['2']['id'] }}" class="form-control">{{ $config['2']['value'] }}</textarea>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-lg-2 control-label">{{ $config['3']['remark'] }}</label>
                                            <div class="col-lg-9">
                                                <textarea name="{{ $config['3']['id'] }}" class="form-control">{{ $config['3']['value'] }}</textarea>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-lg-2 control-label">{{ $config['4']['remark'] }}</label>
                                            <div class="col-lg-9 send">
                                                <img id="preview" src="{{ $config['4']['value'] }}">
                                                <input type="file" name="{{ $config['4']['id'] }}" id="doc" onchange="javascript:setImagePreview('doc');" />
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-lg-2 control-label">{{ $config['5']['remark'] }}</label>
                                            <div class="col-lg-9"><input type="text" name="{{ $config['5']['id'] }}" class="form-control" value="{{ $config['5']['value'] }}">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-lg-2 control-label">{{ $config['6']['remark'] }}</label>
                                            <div class="col-lg-9"><input type="text" name="{{ $config['6']['id'] }}" class="form-control" value="{{ $config['6']['value'] }}">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-lg-2 control-label">{{ $config['10']['remark'] }}</label>
                                            <div class="col-lg-9">
                                            <textarea name="{{ $config['10']['id'] }}" class="form-control">{{ $config['10']['value'] }}</textarea>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-lg-2 control-label">{{ $config['20']['remark'] }}</label>
                                            <div class="col-lg-9 send">
                                                <img id="preview1" src="{{ $config['20']['value'] }}">
                                                <input type="file" name="{{ $config['20']['id'] }}" id="doc1" onchange="javascript:setImagePreview1('doc1');" />
                                            </div>
                                        </div>
          <!--                               <div class="form-group">
                                            <label class="col-lg-2 control-label">{{ $config['20']['remark'] }}</label>
                                            <div class="col-lg-9"><input type="file" name="{{ $config['20']['id'] }}" class="form-control" value="{{ $config['20']['value'] }}">
                                            </div>
                                        </div> -->
                                        <div class="form-group">
                                            <label class="col-lg-2 control-label">{{ $config['11']['remark'] }}</label>
                                            <div class="col-lg-9"><input type="text" name="{{ $config['11']['id'] }}" class="form-control" value="{{ $config['11']['value'] }}">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-lg-2 control-label">{{ $config['12']['remark'] }}</label>
                                            <div class="col-lg-9"><input type="text" name="{{ $config['12']['id'] }}" class="form-control" value="{{ $config['12']['value'] }}">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-lg-2 control-label">{{ $config['13']['remark'] }}</label>
                                            <div class="col-lg-9"><input type="text" name="{{ $config['13']['id'] }}" class="form-control" value="{{ $config['13']['value'] }}">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-lg-2 control-label">{{ $config['14']['remark'] }}</label>
                                            <div class="col-lg-9"><input type="text" name="{{ $config['14']['id'] }}" class="form-control" value="{{ $config['14']['value'] }}">
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div id="tab-2" class="tab-pane">
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="col-lg-2 control-label">{{ $config['15']['remark'] }}</label>
                                            <div class="col-lg-9"><input type="text" name="{{ $config['15']['id'] }}" class="form-control" value="{{ $config['15']['value'] }}">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-lg-2 control-label">{{ $config['16']['remark'] }}</label>
                                            <div class="col-lg-9"><input type="text" name="{{ $config['16']['id'] }}" class="form-control" value="{{ $config['16']['value'] }}">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-lg-2 control-label">{{ $config['17']['remark'] }}</label>
                                            <div class="col-lg-9"><input type="text" name="{{ $config['17']['id'] }}" class="form-control" value="{{ $config['17']['value'] }}">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-lg-2 control-label">{{ $config['18']['remark'] }}</label>
                                            <div class="col-lg-9"><input type="text" name="{{ $config['18']['id'] }}" class="form-control" value="{{ $config['18']['value'] }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="tab-3" class="tab-pane">
                                    <div class="panel-body">
                                        {{--<div class="form-group">--}}
                                            {{--<label class="col-lg-2 control-label">{{ $config['7']['remark'] }}</label>--}}
                                            {{--<div class="col-lg-9"><input type="text" name="{{ $config['7']['id'] }}" class="form-control" value="{{ $config['7']['value'] }}">--}}
                                            {{--</div>--}}
                                        {{--</div>--}}

                                        <div class="form-group">
                                            <label class="col-lg-2 control-label">{{ $config['8']['remark'] }}</label>
                                            <div class="col-lg-9"><input type="text" name="{{ $config['8']['id'] }}" class="form-control" value="{{ $config['8']['value'] }}">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-lg-2 control-label">{{ $config['9']['remark'] }}</label>
                                            <div class="col-lg-9"><input type="text" name="{{ $config['9']['id'] }}" class="form-control" value="{{ $config['9']['value'] }}">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-lg-2 control-label">{{ $config['19']['remark'] }}</label>
                                            <div class="col-lg-9"><input type="text" name="{{ $config['19']['id'] }}" class="form-control" value="{{ $config['19']['value'] }}">(每笔最低提现金额)
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label"></label>
                                    <div class="col-sm-9">
                                        <button type="submit" class="btn btn-success">保存配置</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        //下面用于图片上传预览功能
        function setImagePreview(id, avalue) {
            var docObj = document.getElementById(id);

            var imgObjPreview = document.getElementById("preview");
            if (docObj.files && docObj.files[0]) {
//火狐下，直接设img属性
                imgObjPreview.style.display = 'block';
                imgObjPreview.style.width = '125px';
                imgObjPreview.style.height = '125px';
//imgObjPreview.src = docObj.files[0].getAsDataURL();

//火狐7以上版本不能用上面的getAsDataURL()方式获取，需要一下方式
                imgObjPreview.src = window.URL.createObjectURL(docObj.files[0]);
            }
            else {
//IE下，使用滤镜
                docObj.select();
                var imgSrc = document.selection.createRange().text;
                var localImagId = document.getElementById("localImag");
//必须设置初始大小
                localImagId.style.width = "36px";
                localImagId.style.height = "36px";
//图片异常的捕捉，防止用户修改后缀来伪造图片
                try {
                    localImagId.style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale)";
                    localImagId.filters.item("DXImageTransform.Microsoft.AlphaImageLoader").src = imgSrc;
                }
                catch (e) {
                    alert("您上传的图片格式不正确，请重新选择!");
                    return false;
                }
                imgObjPreview.style.display = 'none';
                document.selection.empty();
            }
            return true;
        }
        function setImagePreview1(id, avalue) {
            var docObj = document.getElementById(id);

            var imgObjPreview = document.getElementById("preview1");
            if (docObj.files && docObj.files[0]) {
//火狐下，直接设img属性
                imgObjPreview.style.display = 'block';
                imgObjPreview.style.width = '125px';
                imgObjPreview.style.height = '125px';
//imgObjPreview.src = docObj.files[0].getAsDataURL();

//火狐7以上版本不能用上面的getAsDataURL()方式获取，需要一下方式
                imgObjPreview.src = window.URL.createObjectURL(docObj.files[0]);
            }
            else {
//IE下，使用滤镜
                docObj.select();
                var imgSrc = document.selection.createRange().text;
                var localImagId = document.getElementById("localImag");
//必须设置初始大小
                localImagId.style.width = "36px";
                localImagId.style.height = "36px";
//图片异常的捕捉，防止用户修改后缀来伪造图片
                try {
                    localImagId.style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale)";
                    localImagId.filters.item("DXImageTransform.Microsoft.AlphaImageLoader").src = imgSrc;
                }
                catch (e) {
                    alert("您上传的图片格式不正确，请重新选择!");
                    return false;
                }
                imgObjPreview.style.display = 'none';
                document.selection.empty();
            }
            return true;
        }
    </script>
@endsection