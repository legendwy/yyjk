@extends('layouts.admin')
@section('title', '退款退货理由列表')
@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>退款退货理由管理</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('admin') }}">控制台</a>
                </li>
                <li class="active">
                    <strong>退款退货理由列表</strong>
                </li>
            </ol>
        </div>
        <div class="col-lg-2">
            @permission(('reason.add'))
            <h2><a class="btn btn-primary btn-outline" href="{{ url('admin/reason/create') }}">添加退款退货理由</a></h2>
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
                        <table class="table  table-hover">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>理由</th>
                                <th width="120">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($list as $item)
                                <tr>
                                <td>{{ $item->id }} </td>
                                <td>{{ $item->title }} </td>
                                <td>
                                    @permission(('reason.edit'))
                                    <a href="{{ url('admin/reason/'.$item['id'].'/edit') }}" class="btn btn-primary btn-outline btn-xs" title="编辑"><span class="fa fa-edit"></span></a>
                                    @endpermission
                                    @permission(('reason.delete'))
                                    <a onclick="del('{{ $item['id'] }}')" class="btn btn-danger btn-outline btn-xs" title="删除"><form name="delete-{{ $item['id'] }}" action="{{ url('admin/reason/'.$item['id'].'') }}" method="post">{!! csrf_field() !!}<input type="hidden" name="_method" value="delete"></form><span class="fa fa-trash"></span></a>
                                    @endpermission
                                </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="text-center">
                            {{ $list->links() }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection