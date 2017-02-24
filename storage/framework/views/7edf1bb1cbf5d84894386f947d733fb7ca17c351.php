

<?php $__env->startSection('content'); ?>
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">Api实现登录注册</div>
                    <div class="panel-body">
                        <div class="col-sm-12">
                            <a href="<?php echo e(url('api/users')); ?>" class="btn btn-success">查看用户列表</a>
                        </div>
                        <div class="col-sm-6">
                            <h2>登录</h2>
                            <form id="form" action="<?php echo e(url('api/login')); ?>" class="form-group" method="post">
                                <input name="email" type="email" class="form-control" placeholder="邮箱">
                                <br/>
                                <input type="password" name="password" class="form-control" placeholder="密码">
                                <br>
                                <input class="btn btn-primary" value="提交" type="submit">
                            </form>
                        </div>
                        <div class="col-sm-6">
                            <h2>注册</h2>
                            <form id="form" action="<?php echo e(url('api/register')); ?>" class="form-group" method="post">
                                <input name="name" type="text" class="form-control" placeholder="昵称">
                                <br/>
                                <input name="email" type="email" class="form-control" placeholder="邮箱">
                                <br/>
                                <input type="password" name="password" class="form-control" placeholder="密码">
                                <br>
                                <input class="btn btn-primary" value="提交" type="submit">
                            </form>
                        </div>
                        <div class="col-sm-12 m-t-lg">
                            <br/>
                            <br/>
                            <br/>
                            <h2>测试资源路由</h2>
                            <button class="btn btn-success" onclick="getAll()">GET article</button>
                            <button class="btn btn-warning" onclick="get()">GET article/1</button>
                            <button class="btn btn-primary" onclick="post()">POST article</button>
                            <button class="btn btn-default" onclick="put()">PUT article/1</button>
                            <button class="btn btn-danger" onclick="deleteArticle()">DELETE article/1</button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function ss() {
            var data = $('#form').serialize();
            $.ajax({
                url: '<?php echo e(url('logs')); ?>',
                type: 'post',
                data: data
            })
            .success(function(data) {
                $('#content').html(data);
            })
            .error(function(data) {
                console.log(data);
            });
//            alert(data);
        }
        function getAll() {
            $.ajax({
                url: '<?php echo e(url('logs')); ?>',
                type: 'get'
            })
            .success(function(data) {
                $('#content').html(data);
            })
            .error(function(data) {
                console.log(data);
            });
        }
        function get() {
            $.ajax({
                url: '<?php echo e(url('logs/1')); ?>',
                type: 'get'
            })
            .success(function(data) {
                $('#content').html(data);
            })
            .error(function(data) {
                console.log(data);
            });
        }
        function post() {
            
                
                
            
            $.ajax({
                headers: {
                    Authorization: "Bearer  Token"
                },
                url: 'http://yyjk.com/logs',
                type: 'post',
                data: {title:"测试测试",content:'测试测试'}
            })
            .success(function(data) {
                $('#content').html(data);
            })
            .error(function(data) {
                console.log(data);
            });
        }
        function put() {
            
                
                
            
            $.ajax({
                url: '<?php echo e(url('logs/18')); ?>',
                type: 'put',
                data: {title:'修改啊啊是啊', 'content': 'sdads撒打算'}
            })
            .success(function(data) {
                $('#content').html(data);
            })
            .error(function(data) {
                console.log(data);
            });
        }
        function deleteArticle() {
            
                
                
            
            $.ajax({
                url: '<?php echo e(url('logs/5')); ?>',
                type: 'delete'
            })
            .success(function(data) {
                $('#content').html(data);
            })
            .error(function(data) {
                console.log(data);
            });
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>