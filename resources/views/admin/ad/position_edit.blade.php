@extends('layouts.admin')
@section('title', '修改广告位')
@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>广告管理</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('admin') }}">控制台</a>
                </li>
                <li>
                    <a href="{{ url('admin/article') }}">广告列表</a>
                </li>
                <li class="active">
                    <strong>修改广告位</strong>
                </li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="wrapper wrapper-content animated ">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title" >
                        <span>修改广告位</span>
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
                            <div class="form-group"><label class="col-lg-2 control-label">广告位名称</label>
                                <div class="col-lg-9"><input type="text" name="title" class="form-control" value="{{ $position->name }}">
                                </div>
                            </div>

                            <div class="form-group"><label class="col-lg-2 control-label">尺寸大小</label>
                                <div class="col-lg-9"><input type="text" name="sort" class="form-control">
                                </div>
                            </div>

                            <div class="form-group"><label class="col-lg-2 control-label">是否显示</label>
                                <div class="col-lg-9">
                                    <input type="radio" value="1" name="status" checked>显示&nbsp;&nbsp;&nbsp;
                                    <input type="radio" value="0" name="status">不显示
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