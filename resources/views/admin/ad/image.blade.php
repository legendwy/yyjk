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
                    <strong>欢迎图列表</strong>
                </li>
            </ol>
        </div>
    </div>
    <div class="wrapper wrapper-content  animated">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        @include('flash::message')
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>图片</th>
                                <th>尺寸</th>
                                <th>创建时间</th>
                                <th width="120">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($list as $item)
                                <tr>
                                    <td><img src="{{ $item['image'] }}" style="width:100px;height: 150px;"/></td>
                                    <td>{{ $item['size'] }}</td>
                                    <td>{{ $item['created_at'] }}</td>
                                    <td>
                                        @permission(('guang.edit'))
                                        <a class="btn btn-primary btn-outline btn-xs edit" href="{{ url('admin/image/'.$item['id'].'/edit') }}" title="编辑"> <span class="fa fa-edit"></span> </a>
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