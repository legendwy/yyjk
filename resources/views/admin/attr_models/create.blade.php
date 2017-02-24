@extends('layouts.admin')
@section('title', '添加规格模型')
<style>
    .xxx input{
        border-color: #C9C5C5;
    }
</style>
@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>规格模型管理</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('admin') }}">控制台</a>
                </li>
                <li>
                    <a href="{{ url('admin/attr_models') }}">规格模型列表</a>
                </li>
                <li class="active">
                    <strong>添加规格模型</strong>
                </li>
            </ol>
        </div>
    </div>
    <div class="row">
    <div class="wrapper wrapper-content animated ">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <span>添加规格模型</span>
                </div>
                <div class="ibox-content">
                    <form class="form form-horizontal" id="add" method="post">
                        <div class="form-group">
                            <label class="col-sm-1 control-label">模型名：</label>
                            <div class="col-sm-9">
                                <input name="name" type="text" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-1 control-label">模型属性：</label>
                            <div class="col-sm-11">
                                <button class="btn btn-outline btn-success" type="button" onclick="addAttr()">添加属性</button>
                                <table class="table table-hover xxx">
                                    <thead>
                                    <tr>
                                        <th width="10%">属性</th>
                                        <th width="5%">操作</th>
                                        <th width="85%">属性值</th>
                                    </tr>
                                    </thead>
                                    <tbody class="table table-hover">
                                        <tr id="tablexx"></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label"></label>
                            <div class="col-sm-9">
                                <button class="btn btn-success btn-block"  type="button" id="sub" onclick="subAttr()" >提交数据</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
    <script>
        var id = 1;
        function addAttr() {
            var str = '<tr style="margin-top: 20px;" class="tr_'+id+'">' +
                    '<td><input class="form-control" style="width: 90px; float: left" name="data['+id+'][attr]"><a onclick="del_attr('+id+')" style="float: left; color: red"><span class="fa fa-times"></span></a></td>' +
                    '<td><button class="btn btn-warning btn-outline add-attr" type="button" onclick="add_attr('+id+')" style="float: left; margin-right: 10px;">添加</button></td>' +
                    '<td class="aaa_'+id+'"><div class="_td_0"> <input class="form-control" style="width: 70px; float: left; margin-left: 10px;" name="data['+id+'][attrValue][]"><a onclick="del_attr_value('+id+', 0)" style="float: left; color: red"><span class="fa fa-times"></span></a></div> <span class="td_'+id+'"></span></td>' +
                    '</tr>';
            $('#tablexx').before(str);
            id = id + 1;
        }
        function add_attr(id) {
            var num = $(".aaa_"+id+" div").length;
            var td = '<div class="_td_'+num+'"><input class="form-control" style="width: 70px; float: left; margin-left: 10px;" name="data['+id+'][attrValue][]"><a onclick="del_attr_value('+id+', '+num+')" style="float: left; color: red"><span class="fa fa-times"></span></a></div>';
            $('.td_'+id).before(td);
        }
        /**
         * 删除属性
         * @param id
         */
        function del_attr(id) {
            $('.tr_'+id).remove();
        }
        /**
         * 删除属性值
         */
        function del_attr_value(id, num) {
            $('.aaa_'+id+' ._td_'+num).remove()
        }
        function subAttr() {
            var data = $('#add').serialize();
//            alert(data);
            $.ajax({
                type: "post",
                data: data,
                url: '{{ url('admin/attr_models') }}',
                beforeSend: function () {
                    layer.load(0);
                    $('#sub').attr('disabled', true)
                },
                success: function (data) {
                    if (data.status == 'success'){
                        layer.msg('添加成功！', {icon:1, time:800}, function () {
                            window.location.href="{{ url('admin/attr_models') }}"
                        });
                    }else {
                        layer.msg('添加失败！', {icon:0});
                    }
                },
                complete: function () {//完成响应
                    layer.closeAll('loading');
                    $('#sub').attr('disabled', false)
                },
                error: function (data) {
                    data = data.responseJSON;
                    var str = '';
                    for (var i in data){
                        str += data[i][0]+'， ';
                    }
                    if (str != ''){
                        layer.alert(str)
                    }else {
                        layer.alert('系统异常')
                    }
                }
            });
        }
    </script>
    @endsection