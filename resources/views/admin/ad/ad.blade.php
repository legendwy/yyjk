@extends('layouts.admin')
@section('title', '广告列表')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>广告管理</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('admin') }}">控制台</a>
                </li>
                <li class="active">
                    <strong>广告列表</strong>
                </li>
            </ol>
        </div>
        <div class="col-lg-2">
            @permission(('guang.add'))
            <h2><a class="btn btn-primary btn-outline" href="{{ url('admin/guang/create/?position_id='.$id) }}">添加轮播图</a></h2>
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
                                <th>所属广告位</th>
                                <th>图片</th>
                                <th>路径</th>
                                <th>图片尺寸</th>
                                <th>创建时间</th>
                                <th>排序</th>
                                <th width="120">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($list as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td><img src="{{ $item->image }}" style="width:100px;height: 50px;"/></td>
                                    <td><a href="{{ $item->url }}" target="_blank">{{ $item->url }}</a></td>
                                    <td>{{ $item->size }}</td>
                                    <td>{{ $item->created_at }}</td>
                                    <td>{{ $item->sort }}</td>
                                    <td>
                                        @permission(('guang.edit'))
                                        <a class="btn btn-primary btn-outline btn-xs edit" href="{{ url('admin/guang/'.$item->id.'/edit') }}" title="编辑"> <span class="fa fa-edit"></span> </a>
                                        @endpermission
                                        @permission(('guang.delete'))
                                        <a  onclick="del('{{ $item['id'] }}')" class="btn btn-danger btn-outline btn-xs edit" title="删除"><form name="delete-{{ $item->id }}" action="{{ url('admin/guang/'.$item->id.'') }}" method="post">{!! csrf_field() !!}<input type="hidden" name="_method" value="delete"></form> <span class="fa fa-trash"></span> </a>
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