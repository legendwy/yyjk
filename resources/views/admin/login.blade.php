<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登陆后台</title>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('font-awesome/css/font-awesome.css') }}" rel="stylesheet">
    <link href="{{ asset('css/animate.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<body class="gray-bg">
<div class="loginColumns animated fadeInDown">
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="ibox-content">
                <form class="form-horizontal" role="form" method="POST" action="{{ url('admin/login') }}">
                    <div class="form-group">
                        <div class="col-md-12 text-center">
                            <h2 class="font-bold"><span class="fa fa-user"></span> 登录后台</h2>
                        </div>
                    </div>
                    {!! csrf_field() !!}
                    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                        <div class="col-md-12">
                            <input type="name" class="form-control" name="name" placeholder="管理员名称" value="{{ old('name') }}">
                            @if ($errors->has('name'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                        <div class="col-md-12">
                            <input type="password" class="form-control" placeholder="密码" name="password">
                            @if ($errors->has('password'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group{{ $errors->has('captcha') ? ' has-error' : '' }}">
                        <div class="col-md-8">
                            <input type="text" class="form-control" placeholder="验证码" name="captcha" >
                            @if ($errors->has('captcha'))
                                <span class="help-block">
                    <strong>{{ $errors->first('captcha') }}</strong>
                </span>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <img src="{{ captcha_src() }}" onclick="this.src='{{ captcha_src() }}'+Math.random()" style='cursor: pointer;'>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-6 m-b">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="remember"> 记住登录
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fa fa-btn fa-sign-in"></i> 登录
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>