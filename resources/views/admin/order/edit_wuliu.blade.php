@extends('layouts.admin.header')
@section('title', '订单发货')
@section('content')
<div class="row">
    <div class="wrapper wrapper-content animated">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <form class="form-horizontal" id="form">
                        @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="form-group"><label class="col-lg-2 control-label">选择物流公司</label>
                            <div class="col-lg-9">
                                <select name="wuliu_gongsi" class="form-control">
                                    @foreach($wuliu as $k)
                                        <option value="{{ $k->id }}" @if($wuliu_info->wuliu_gongsi == $k->id) selected @endif>{{ $k->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        {!! csrf_field() !!}
                        <div class="form-group"><label class="col-lg-2 control-label">请填写运单号</label>
                            <div class="col-lg-9">
                                <input class="form-control" name="wuliu_num" value="{{ old('wuliu_num', $wuliu_info->wuliu_num) }}">
                            </div>
                        </div>
                        <div class="form-group"><label class="col-lg-2 control-label"></label>
                            <div class="col-lg-9">
                                <button type="button" class="btn btn-primary btn-block" id="sub" onclick="fahuo('{{ $id }}')">提交</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function fahuo(id) {
        var data = $('#form').serialize();
        $.ajax({
            type: "post",
            data: data,
            url: '{{ url('admin/edit_wuliu') }}/'+id,
            beforeSend: function () {
                layer.load(0);
            },
            success: function (data) {
                if (data.status == 'success'){
                    layer.msg('修改成功！', {icon: 1, time:800}, function () {
                        parent.window.location.reload();
                    });
                }else {
                    layer.alert(data.msg);
                }
            },
            complete: function () {
                //完成响应
                layer.closeAll('loading');
            },
            error: function (data) {
                layer.alert('系统异常')
            }
        });
    }
</script>
    @endsection
