<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>sshop</title>

    <!-- Bootstrap core CSS -->
    <link href="{{ URL::asset('css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="{{ URL::asset('css/jumbotron-narrow.css') }}" rel="stylesheet">
</head>

<body>

<div class="container">
    <div class="header clearfix">
        <nav>
            <ul class="nav nav-pills pull-right">
                <li role="presentation"><a href="/shop">商品列表</a></li>
                @php
                    try {
                        if (session('username')) {
                        echo '<li role="presentation"><a href="/user">个人中心</a></li>
                            <li role="presentation"><a href="/seckill">！秒杀活动！</a></li>
                            <li role="presentation"><a href="/shopcar">购物车</a></li>
                            <li role="presentation"><a href="/user/change">修改密码</a></li>
                            <li role="presentation"><a href="/logout">注销</a></li>';
                        } else {
                        echo '<li role="presentation"><a href="/login">登录</a></li>
                            <li role="presentation"><a href="/register">注册</a></li>';
                        }
                    } catch (\ErrorException $e) {
                        echo '<li role="presentation"><a href="/login">登录</a></li>
                            <li role="presentation"><a href="/register">注册</a></li>';
                    }
                @endphp
            </ul>
        </nav>
        <h3 class="text-muted">sshop</h3>
    </div>
    @php
        try{
            if ($success) {
            echo "
            <div class=\"alert alert-success alert-dismissable\">
                操作成功。
            </div>";
            }
        } catch (\ErrorException $e) {

        }
    @endphp

    @php
        try{
            if ($danger) {
            echo "
            <div class=\"alert alert-danger alert-dismissable\">
                操作失败。
            </div>";
            }
        } catch (\ErrorException $e) {

        }
    @endphp
    @yield('body')

</div> <!-- /container -->
</body>
</html>
