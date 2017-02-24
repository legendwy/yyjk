@extends('layouts.admin.header')
@section('title', '添加管理员')

@section('content')
    <div class="wrapper wrapper-content  animated ">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        @include('flash::message')
                        <table style="border: 0" class="table table-bordered table-hover">
                            <tbody>
                            <thead>
                            <tr>
                                <th>名字</th>
                                <th>注册时间</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($childs_list as $item)
                                <tr>
                                    <td>
                                        @if(!$item['name'])
                                            {{ $item['nickname'] }}
                                        @else
                                            {{ $item['name'] }}
                                        @endif
                                    </td>
                                    <td>
                                        {{ $item['created_at'] }}
                                    </td>
                                </tr>
                                @if(!empty($item['child']))
                                <tr>
                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        @if(!$item['child']['name'])
                                            {{ $item['child']['nickname'] }}
                                        @else
                                            {{ $item['child']['name'] }}
                                        @endif
                                    </td>
                                    <td>
                                        {{ $item['child']['created_at'] }}
                                    </td>
                                </tr>
                                @endif
                            @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection