<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>电话会议系统管理后台</title>
    <link href="{{ URL::asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-datetimepicker.css') }}" type="text/css" />
</head>
<body id="app-layout">

<div class="tips-toast"></div>
<style>
    .spinner {
        width: 40px;
        height: 40px;
        background-color: #60a3ff;
        position: absolute;
        left: 50%;
        top: 50%;
        border-radius: 100%;
        -webkit-animation: scaleout 1.0s infinite ease-in-out;
        animation: scaleout 1.0s infinite ease-in-out;
    }

    @-webkit-keyframes scaleout {
        0% { -webkit-transform: scale(0.0) }
        100% {
            -webkit-transform: scale(1.0);
            opacity: 0;
        }
    }

    @keyframes scaleout {
        0% {
            transform: scale(0.0);
            -webkit-transform: scale(0.0);
        } 100% {
              transform: scale(1.0);
              -webkit-transform: scale(1.0);
              opacity: 0;
          }
    }
</style>

@include('admin.layouts.aside')

    <section class="app-main">
        @include('admin.layouts.header')
        <section>
            @yield('content')
        </section>
    </section>

<!-- JavaScripts -->
<script src="{{ URL::asset('js/jquery.min.js') }}"></script>
{{--<script src="{{ URL::asset('js/jquery.knob-1.0.1.js') }}"></script>--}}
{{--<script src="{{ URL::asset('js/chartjs/echarts.min.js') }}"></script>--}}
{{--<script src="{{ URL::asset('js/chartjs/wonderland.js') }}"></script>--}}
<script src="{{ URL::asset('js/bootstrap.min.js') }}"></script>
<script src="{{ URL::asset('js/vue/vue.min.js') }}"></script>


<script type="text/javascript" src="{{ URL::asset('js/datetimepicker/bootstrap-datetimepicker.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/datetimepicker/locales/bootstrap-datetimepicker.zh-CN.js') }}" charset="UTF-8"></script>
<script src="{{ URL::asset('js/bootstrap.min.js') }}"></script>
<script src="{{ URL::asset('js/bootstrapValidator.min.js') }}"></script>
<script src="{{ URL::asset('js/common.js') }}"></script>
</body>
</html>
