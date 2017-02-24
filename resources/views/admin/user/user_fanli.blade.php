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
                                <th>来源</th>
                                <th>返利</th>
                                <th>详情</th>
                                <th>时间</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($fanli_list as $item)
                                <tr>
                                    <td>
                                        @if(!$item->name)
                                            {{ $item->nickname }}
                                        @else
                                            {{ $item->name }}
                                        @endif
                                    </td>
                                    <td>
                                        ￥{{ $item->credit }}元
                                    </td>
                                    <td>
                                        {{ $item->remark }}
                                    </td>
                                    <td>
                                        {{ $item->created_at }}
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