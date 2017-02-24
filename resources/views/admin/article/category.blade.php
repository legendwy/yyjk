@extends('layouts.admin')
@section('title', '文章分类')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>分类管理</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('admin') }}">控制台</a>
                </li>
                <li class="active">
                    <strong>文章分类</strong>
                </li>
            </ol>
        </div>
        <div class="col-lg-2">
            @permission(('article_category.add'))
            <h2><a class="btn btn-primary btn-outline" href="{{ url('admin/article_category/create') }}">添加分类</a></h2>
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
                    <div class="ibox-content">
                        @include('flash::message')
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>#ID</th>
                                <th>分类名称</th>
                                <th width="120">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($list as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>
                                        @permission(('article_category.edit'))
                                        <a class="btn btn-primary btn-outline btn-xs edit" href="{{ url('admin/article_category/'.$item->id.'/edit') }}" title="编辑"> <span class="fa fa-edit"></span> </a>
                                        @endpermission

                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection