// $(document).pjax("a[href!='#']", '.pjax');
// $(document).on('pjax:start', function() { NProgress.start(); });
// $(document).on('pjax:end',   function() { NProgress.done();  });
// $(document).on("pjax:timeout", function(event) {
//     // 阻止超时导致链接跳转事件发生
//     event.preventDefault()
// });
//删除
function del(id) {
    layer.confirm('你确认删除此条记录吗吗？', {
        btn: ['确认','取消']
    }, function(){
        $('form[name=delete-'+id+']').submit();
    });
}
//打开子窗口
function layeropen(url, title, width, height) {
    layer.open({
        type: 2,
        title: title,
        shadeClose: true,
        shade: 0.8,
        area: [width, height],
        content: url
    });
}