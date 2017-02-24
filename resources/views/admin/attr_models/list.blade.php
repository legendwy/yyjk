@extends('layouts.admin')
@section('title', '用户列表')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>规格属性管理</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ url('admin') }}">控制台</a>
            </li>
            <li class="active">
                <strong>规格属性列表</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2">
        @permission(('attr_models.add'))
        <h2><a class="btn btn-primary btn-outline" href="{{ url('admin/attr_models/create') }}">添加规格属性</a></h2>
        @endpermission
    </div>
</div>
<div class="wrapper wrapper-content  animated">
    {{--<p class="font-bold  alert alert-warning m-b-sm">--}}
        {{--<i class="fa fa-lightbulb-o"></i> &nbsp;非专业人士请勿操作--}}
    {{--</p>--}}
    <div class="row">
        <div class="col-lg-6">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    @include('flash::message')
                    <table class="table table-hover  table-bordered">
                        <thead>
                        <tr>
                            <th>#ID</th>
                            <th>模型名称</th>
                            {{--<th>模型属性</th>--}}
                            <th width="120">操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($list as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->name }}</td>
{{--                            <td>{{ $item->attr }}</td>--}}
                            <td>
                                @permission(('attr_models.edit'))
                                <a class="btn btn-primary btn-outline btn-xs edit" href="{{ url('admin/attr_models/'.$item->id.'/edit') }}" title="编辑"> <span class="fa fa-edit"></span> </a>
                                @endpermission
                                @permission(('attr_models.delete'))
                                <a  onclick="del('{{ $item->id }}')" class="btn btn-danger btn-outline btn-xs edit" title="删除"><form name="delete-{{ $item->id }}" action="{{ url('admin/attr_models/'.$item->id.'') }}" method="post">{!! csrf_field() !!}<input type="hidden" name="_method" value="delete"></form> <span class="fa fa-trash"></span> </a>
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