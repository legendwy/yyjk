@extends('layouts.admin.header')
@section('title', '区域代理收益')

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
                                <th>时间</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($fanli_list as $item)
                                <tr>
                                    <td>
                                        ￥{{ $item->money }}
                                    </td>
                                    <td>
                                        {{ $item->use }}
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