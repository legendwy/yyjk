@extends('layouts.admin.header')
@section('title', '添加管理员')

@section('content')
<div class="wrapper wrapper-content  animated ">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    @include('flash::message')
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>省</th>
                            <th>市</th>
                            <th>区</th>
                            <th>街道信息</th>
                            <th>详细地址</th>
                            <th>收货人姓名</th>
                            <th>手机号码</th>
                            <th>默认地址</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($address_list as $item)
                            <tr>
                                <td>{{ $item->province }} </td>
                                <td>{{ $item->city }}</td>
                                <td>{{ $item->area }}</td>
                                <td>{{ $item->street }}</td>
                                <td>{{ $item->address }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->phone }}</td>
                                <td>
                                    @if($item->status == 1)
                                        是
                                    @else
                                        否
                                    @endif
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