@extends('layouts.admin')
@section('title', '添加文章')

@section('content')
@include('UEditor::head')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>文章管理</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('admin') }}">控制台</a>
                </li>
                <li>
                    <a href="{{ url('admin/article') }}">文章列表</a>
                </li>
                <li class="active">
                    <strong>添加文章</strong>
                </li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="wrapper wrapper-content animated ">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <span>添加文章</span>
                    </div>
                    <div class="ibox-content">
                        <form class="form-horizontal" id="form" action="{{ url('admin/article') }}" method="post">
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
                            {!! csrf_field() !!}
                            <div class="form-group"><label class="col-lg-2 control-label">*文章标题</label>
                                <div class="col-lg-9"><input type="text" name="title" value="{{ old('title') }}" class="form-control">
                                </div>
                            </div>

                            <div class="form-group"><label class="col-lg-2 control-label">*分类栏目</label>
                                <div class="col-lg-9">
                                    <select class="form-control" name="type_id">
                                        <option value="">请选择...</option>
                                        @foreach($category as $item)
                                            <option value="{{ $item->id }}" @if($item->id == old('type_id')) selected @endif>{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group"><label class="col-lg-2 control-label">*文章排序</label>
                                <div class="col-lg-9"><input type="text" name="sort" value="{{ old('sort', 0) }}" class="form-control">
                                </div>
                            </div>

                            <div class="form-group"><label class="col-lg-2 control-label">*文章摘要</label>
                                <div class="col-lg-9"><textarea name="desc" cols="" rows="" class="form-control">{{ old('desc') }}</textarea>
                                </div>
                            </div>

                            <div class="form-group"><label class="col-lg-2 control-label">*文章内容</label>
                                <div class="col-lg-9">
                                    <script id="container" name="content" type="text/plain" style="height:500px">{!! old('content') !!}</script>
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

<script type="text/javascript">
    var ue = UE.getEditor('container');
    ue.ready(function() {
        ue.execCommand('serverparam', '_token', '{{ csrf_token() }}');//此处为支持laravel5 csrf ,根据实际情况修改,目的就是设置 _token 值.
    });
</script>
@endsection