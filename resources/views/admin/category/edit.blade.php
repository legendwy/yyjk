@extends('layouts.admin')

@section('title', '编辑菜单')



@section('content')

    @inject('menus', 'App\Repositories\Presenter\MenuPresenter')

    <div class="row wrapper border-bottom white-bg page-heading">

        <div class="col-lg-10">

            <h2>商品分类管理</h2>

            <ol class="breadcrumb">

                <li>

                    <a href="{{ url('admin') }}">控制台</a>

                </li>

                <li>

                    <a href="{{ url('admin/category') }}">商品分类列表</a>

                </li>

                <li class="active">

                    <strong>编辑商品分类</strong>

                </li>

            </ol>

        </div>

    </div>

    <div class="row">

    <div class="wrapper wrapper-content animated ">

        <div class="col-lg-12">

            <div class="ibox float-e-margins">

                <div class="ibox-title">

                    <span>编辑商品分类</span>

                </div>

                <div class="ibox-content">

                    <form class="form-horizontal" id="form" action="{{ url('admin/category/'.$info['id'].'') }}" method="post" enctype="multipart/form-data">

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

                            <div class="form-group">

                                <label  class="col-lg-2 control-label">上级分类：</label>

                                <div class="col-lg-9">

                                    <select class="form-control" name="parent_id">

                                        <option value="0">顶级分类</option>

                                        @foreach($category as $v)

                                            <option value="{{ $v['id'] }}" @if(old('parent_id', $v['id']) == $info['parent_id']) selected @endif>┠  {{ $v['category_name'] }}</option>

                                    
                                        @endforeach

                                    </select>

                                </div>

                            </div>

                            <div class="form-group">

                                    <label class="col-lg-2 control-label">分类名称：</label>

                                    <div class="col-lg-9">

                                    <input type="text" name="category_name" placeholder="分类名称" value="{{ old('category_name', $info['category_name']) }}"  class="form-control">

                                </div>

                            </div>

                            <div class="form-group">



                                    <label class="col-lg-2 control-label">移动端名称：</label>

                                <div class="col-lg-9">

                                    <input type="text" name="mobile_name" placeholder="移动端名称" value="{{ old('mobile_name', $info['mobile_name']) }}"  class="form-control">

                                </div>

                            </div>

                            <div class="form-group">

                                    <label class="col-lg-2 control-label">分类介绍：</label>

                                <div class="col-lg-9">

                                    <textarea name="info" placeholder="分类介绍" class="form-control">{{ old('info', $info['info']) }}</textarea>

                                </div>

                            </div>

                            <div class="form-group">



                                    <label class="col-lg-2 control-label">分类排序：</label>

                                <div class="col-lg-9">

                                    <input type="text" name="sort" placeholder="分类排序" value="{{ old('sort', $info['sort']) }}"  class="form-control">

                                </div>

                            </div>

                            <input type="hidden" name="id" value="{{ $info['id'] }}">

                            {!! method_field('put') !!}

                            {!! csrf_field() !!}

                            <div class="form-group my-icon">

                                    <label class="col-lg-2 control-label">分类图标：</label>

                                <div class="col-lg-9">

                                    <input type="file" name="icon" placeholder="分类图标"  class="form-control">

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