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
                                <th>金额</th>
                                <th>备注</th>
                                <th>状态</th>
                                <th>时间</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($mingxi_list as $item)
                                <tr>
                                    <td>
                                        {{ $item->money }}元
                                    </td>
                                    <td>
                                        {{ $item->use }}
                                    </td>
                                    <td>
                                        @if($item->status == 1)
                                            收入
                                            @else
                                            支出
                                        @endif
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