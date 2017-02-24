@extends('layouts.admin')
@section('title', '文章列表')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>文章管理</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('admin') }}">控制台</a>
                </li>
                <li class="active">
                    <strong>文章列表</strong>
                </li>
            </ol>
        </div>
        <div class="col-lg-2">
            @permission(('article.add'))
            <h2><a class="btn btn-primary btn-outline" href="{{ url('admin/article/create') }}">添加文章</a></h2>
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
                                <th>文章标题</th>
                                <th>文章分类</th>
                                <th>文章排序</th>
                                <th width="120">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($article as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->title }}</td>
                                    <td>{{ $item->type_name }}</td>
                                    <td>{{ $item->sort }}</td>
                                    <td>
                                        @permission(('article.edit'))
                                        <a class="btn btn-primary btn-outline btn-xs edit" href="{{ url('admin/article/'.$item->id.'/edit') }}" title="编辑"> <span class="fa fa-edit"></span> </a>
                                        @endpermission
                                        @permission(('article.delete'))
                                        @if($item->type_id!=3)
                                        <a  onclick="del('{{ $item['id'] }}')" class="btn btn-danger btn-outline btn-xs edit" title="删除"><form name="delete-{{ $item->id }}" action="{{ url('admin/article/'.$item->id.'') }}" method="post">{!! csrf_field() !!}<input type="hidden" name="_method" value="delete"></form> <span class="fa fa-trash"></span> </a>
                                            @else
                                            <a class="btn btn-default btn-outline btn-xs edit disabled" title="删除"><span class="fa fa-trash"></span> </a>
                                        @endif
                                        @endpermission

                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $article->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection