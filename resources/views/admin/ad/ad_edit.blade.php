@extends('layouts.admin')
@section('title', '编辑图片')
@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>广告管理</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('admin') }}">控制台</a>
                </li>
                <li>
                    <a href="{{ url('admin/ad/ad/'.$ad['position_id'] ) }}">广告列表</a>
                </li>
                <li class="active">
                    <strong>编辑图片</strong>
                </li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="wrapper wrapper-content animated ">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <span>编辑图片</span>
                    </div>
                    <div class="ibox-content">
                        <form class="form-horizontal" id="form" action="{{ url('admin/guang/'. $ad['id'] ) }}" method="post" enctype="multipart/form-data">
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
                                {!! method_field('put') !!}
                            <div class="form-group"><label class="col-lg-2 control-label">排序</label>
                                <div class="col-lg-9"><input type="text" name="sort" class="form-control" value="{{ $ad['sort'] }}">
                                </div>
                            </div>

                            <div class="form-group"><label class="col-lg-2 control-label">URL</label>
                                <div class="col-lg-9"><input type="text" name="url" class="form-control" value="{{ $ad['url'] }}">
                                </div>
                            </div>

                            <div class="form-group"><label class="col-lg-2 control-label">图片</label>
                                <div class="col-lg-9">
                                    <img src="{{ $ad['image'] }}" style="width:100px;height: 100px" />
                                    <input type="file" name="image" class="form-control"/>
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
@endsection
