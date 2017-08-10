<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>登录-电话会议系统</title>
    <link href="{{ URL::asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('css/login.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('css/bootstrapValidator.min.css') }}" rel="stylesheet">
</head>
<body>
<section class="app-login">
    <div class="tips-toast"></div>

        <div class="portal--common">
            <div class="panel panel-welcome">
                <div class="panel-heading text-center">电话会议系统管理后台</div>
                <div class="panel-body">
                    <div class="form">
                        <form role="form" action="{{ url('/') }}" method="post" id="loginForm">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <input type="text" name="name" value="" id="username" placeholder="请输入用户名" class="form-control username clear-tips">
                            </div>
                            <div class="form-group">
                                <input type="password" name="password" placeholder="请输入密码" class="form-control clear-tips"/>
                            </div>
                            <div class="form-group">
                                <div class="inline-block width-fifty">
                                    <input type="text" name="code" placeholder="图形验证码" maxlength="4" class="form-control clear-tips"/>
                                </div>
                                <div class="inline-block width-fourty text-left right">
                                    <div class="cursor img-code" onclick="getVerificationCode('img-code')" title="点击更新验证码"></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="inline-block width-fifty">
                                    <input type="checkbox" id="rememberMe"/> 记住用户名
                                </div>
                                <div class="inline-block width-fifty text-right">
                                </div>
                            </div>
                            <div class="clear-float"></div>
                            <div class="form-group">
                                <a  id="validateBtn"
                                    class="btn btn-green btn-form">登录</a>
                            </div>
                            <div class="text-tips">
                                @if (count($errors) > 0)
                                    @foreach ($errors->all() as $error)
                                        <p class="text-danger text-center">{{ $error }}</p>
                                    @endforeach
                                @endif
                            </div>
                            @if (session('result'))
                                <p class="text-success text-center">
                                    {{ session('result') }}
                                </p>
                            @endif
                        </form>
                    </div>
                </div>
            </div>

    </div>
</section>
<!-- JavaScripts -->
<script src="{{ URL::asset('js/jquery.min.js') }}"></script>
<script src="{{ URL::asset('js/bootstrap.min.js') }}"></script>
<script src="{{ URL::asset('js/bootstrapValidator.min.js') }}"></script>
<script src="{{ URL::asset('js/jquery.cookie.min.js') }}"></script>
<script>
    $(document).ready(function () {
        if($.cookie('l7_username')!=undefined){
            $("#rememberMe").attr("checked", true);
        }else{
            $("#rememberMe").attr("checked", false);
        }
        //读取cookie
        if($('#rememberMe:checked').length>0){
            $('#username').val($.cookie('l7_username'));
        }
        $('#validateBtn').click(function(){
            $('#loginForm').bootstrapValidator('validate');
            var res = $('#loginForm').data('bootstrapValidator').isValid();
            if(res==false){
                $('#loginForm').bootstrapValidator('validate');
            } else{
                if($('#rememberMe:checked').length>0){
                    //设置cookie
                    $.cookie('l7_username', $('#username').val(), {expires: 30});
                }else{
                    //清除cookie
                    $.removeCookie('l7_username');
                }
                document.getElementById('loginForm').submit();
            }

        });
        $('#loginForm').bootstrapValidator({
            fields: {
                name: {
                    message: '用户名无效',
                    validators: {
                        notEmpty: {
                            message: '请输入用户名'
                        }
                    }
                },
                code: {
                    validators: {
                        notEmpty: {
                            message: '验证码不能为空'
                        },
                        stringLength: {
                            min: 4,
                            max: 4,
                            message: '请输入4位数的验证码'
                        }
                    }
                },
                password: {
                    validators: {
                        notEmpty: {
                            message: '请输入密码'
                        }
                    }
                }
            }
        });
//        $('input').keyup(function(){
//            //$('.text-tips').hide();
//        });

        getVerificationCode('img-code'); // 验证码
    });


    function getVerificationCode(className) {
        $.ajax({
            url: "captcha-img", context: {}, success: function (res) {
                $('.' + className).html(res);
            }
        });
    }

</script>

</body>
</html>
