@extends('layouts.admin')
@section('title', '添加权限')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>角色管理</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('admin') }}">控制台</a>
                </li>
                <li>
                    <a href="{{ url('admin/role') }}">角色列表</a>
                </li>
                <li class="active">
                    <strong>添加角色</strong>
                </li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="wrapper wrapper-content animated">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <span>添加一个角色</span>
                    </div>
                    <div class="ibox-content">
                        <form class="form-horizontal" id="form" action="{{ url('admin/role') }}" method="post">
                            @if (count($errors) > 0)
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <p class="font-bold  alert alert-warning m-b-sm" style="display: none" id="error"> </p>
                            <div class="form-group"><label class="col-lg-2 control-label">角色</label>
                                <div class="col-lg-9"><input type="text" name="name" value="{{ old('name') }}" class="form-control">
                                </div>
                            </div>
                            {!! csrf_field() !!}
                            <div class="form-group"><label class="col-lg-2 control-label">角色名称</label>
                                <div class="col-lg-9"><input type="text" name="display_name"  value="{{ old('display_name') }}" class="form-control">
                                </div>
                            </div>
                            <div class="form-group"><label class="col-lg-2 control-label">角色介绍</label>
                                <div class="col-lg-9">
                                    <textarea name="description" class="form-control">{{ old('description') }}</textarea>
                                </div>
                            </div>
                            <div class="form-group"><label class="col-lg-2 control-label">权限管理</label>
                                <div class="col-lg-9">
                                    <br/>
                                    @foreach($permissions as $key => $item)
                                    <dl class="permission-list" style="border-bottom: 1px solid #EAEAEA">
                                        <dd style="display: block">
                                            <dl class="cl permission-list2">
                                                <dt style="display: block; float: left; width: 160px">
                                                    <label class=""><input type="checkbox" id="{{ $key }}"> &nbsp;{{ $key }} <br/>({{ permission_config($key) }})</label>
                                                </dt>
                                                <dd style="display: block; float: left;">
                                                    @foreach($item as $_key => $_item)
                                                        <label style="width: 120px" class="">
                                                            <input type="checkbox" value="{{ $_item['id'] }}" name="permission[]" id="{{ $key }}-{{ $_item['id'] }}">
                                                            {{ $_item['desc'] }}({{ $_item['name'] }})</label> &nbsp;&nbsp;&nbsp;&nbsp;
                                                        @endforeach
                                                </dd>
                                                <div style="clear: both"></div>
                                            </dl>
                                        </dd>
                                    </dl>
                                        @endforeach
                                </div>
                            </div>
                            <div class="form-group"><label class="col-lg-2 control-label"></label>
                                <div class="col-lg-9">
                                    <button type="submit" class="btn btn-primary btn-block" id="sub">提交</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(function(){
            $(".permission-list dt input:checkbox").click(function(){
                $(this).closest("dl").find("dd input:checkbox").prop("checked",$(this).prop("checked"));
            });
            $(".permission-list2 dd input:checkbox").click(function(){
                var l =$(this).parent().parent().find("input:checked").length;
                var l2=$(this).parents(".permission-list").find(".permission-list2 dd").find("input:checked").length;
                if($(this).prop("checked")){
                    $(this).closest("dl").find("dt input:checkbox").prop("checked",true);
                    $(this).parents(".permission-list").find("dt").first().find("input:checkbox").prop("checked",true);
                }
                else{
                    if(l==0){
                        $(this).closest("dl").find("dt input:checkbox").prop("checked",false);
                    }
                    if(l2==0){
                        $(this).parents(".permission-list").find("dt").first().find("input:checkbox").prop("checked",false);
                    }
                }
            });

        });
    </script>
@endsection