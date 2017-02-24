@extends('layouts.admin')
@section('title', '拒绝理由填写')
@section('content')
    <div class="wrapper wrapper-content animated ">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <span>拒绝理由</span>
                </div>
                <div class="ibox-content">
                    <form class="form-horizontal" id="form" action="{{ url('admin/withdraw/refuse_reason') }}" method="post">
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
                            {{--{!! method_field('put') !!}--}}
                            {!! csrf_field() !!}
                        <div class="form-group"><label class="col-lg-2 control-label">*拒绝理由</label>
                            <div class="col-lg-9"><textarea name="reason" cols="" rows="" class="form-control"></textarea>
                            </div>
                        </div>
                            <input type="hidden" name="id" value="{{$id}}" />
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
@endsection