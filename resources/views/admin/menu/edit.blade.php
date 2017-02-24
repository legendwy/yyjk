@extends('layouts.admin')
@section('title', '编辑菜单')

@section('content')
    @inject('menus', 'App\Repositories\Presenter\MenuPresenter')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>菜单管理</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('admin') }}">控制台</a>
                </li>
                <li>
                    <a href="{{ url('admin/menu') }}">菜单列表</a>
                </li>
                <li class="active">
                    <strong>编辑菜单</strong>
                </li>
            </ol>
        </div>
    </div>
    <div class="row">
    <div class="wrapper wrapper-content animated ">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <span>编辑后台菜单</span>
                </div>
                <div class="ibox-content">
                    <form class="form-horizontal" id="form" action="{{ url('admin/menu/'.$menu_info['id'].'') }}" method="post">
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
                        <div class="form-group"><label class="col-lg-2 control-label">父级菜单</label>
                            <div class="col-lg-9">
                                <select class="form-control" name="parent_id" >
                                    {!! $menus->getMenuEdit($menu, $menu_info['parent_id']) !!}
                                </select>
                            </div>
                        </div>
                        <div class="form-group"><label class="col-lg-2 control-label">名称</label>
                            <div class="col-lg-9"><input type="text" name="name" value="{{ $menu_info['name'] }}" class="form-control">
                            </div>
                        </div>
                        {!! csrf_field() !!}
                            <input type="hidden" name="_method" value="PUT">
                        <div class="form-group"><label class="col-lg-2 control-label">权限</label>
                            <input type="hidden" name="id" value="{{ $menu_info['id'] }}">
                            <div class="col-lg-9"><input type="text" name="slug"  value="{{ $menu_info['slug'] }}" class="form-control">
                            </div>
                        </div>
                        <div class="form-group"><label class="col-lg-2 control-label">图标</label>
                            <div class="col-lg-9"><input type="text" name="icon"  value="{{ $menu_info['icon'] }}" class="form-control">
                            </div>
                        </div>
                        <div class="form-group"><label class="col-lg-2 control-label">链接</label>
                            <div class="col-lg-9"><input type="text" name="url"  value="{{ $menu_info['url'] }}"  class="form-control">
                            </div>
                        </div>
                        <div class="form-group"><label class="col-lg-2 control-label">高亮</label>
                            <div class="col-lg-9"><input type="text" name="heightlight_url"  value="{{ $menu_info['heightlight_url'] }}"  class="form-control">
                            </div>
                        </div>
                        <div class="form-group"><label class="col-lg-2 control-label">排序</label>
                            <div class="col-lg-9"><input type="text" name="sort" value="{{ $menu_info['sort'] }}" class="form-control">
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