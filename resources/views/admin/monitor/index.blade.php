@extends('admin.layouts.app')

@section('content')
    <section class="app-content">
        <ol class="breadcrumb">
            <li> <i class="iconfont icon-index block"></i></li>
            <li><a href="{{ url('admin') }}">首页</a></li>
            <li class="active">监控数据</li>
        </ol>
        <section class="child-content">

            <section class="panel panel-default pos-rlt clearfix ">
                <div class="sectionnewWrap">
                    <header class="panel-heading">
                        <div class="h5">
                            <a class="tab-data cursor tab-active" data-id="today">呼叫数量统计</a>
                        </div>
                    </header>
                    <div class="panel-body clearfix border-top-none remove-padding">
                        <div class="row m-l-none m-r-none bg-light lter">
                            <div class="col-md-3 padder-v fix-padding" >
                                <div class="warpbox">
                                    <div class="">
                                        <i class="icon iconfont icon-incoming bigicon"></i>
                                        <span class="green money datatoday">188</span>
                                    </div>
                                    <div class="middle-font-size middle-font-color" >
                                        累计呼出总数（次）
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 padder-v fix-padding ">
                                <div class="warpbox">
                                    <div class="">
                                        <i class="icon iconfont icon-exhale bigicon"></i>
                                        <span class="green money datatoday">123456</span>
                                    </div>
                                    <div class="middle-font-size middle-font-color" >
                                        累计呼入总数（次）
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 padder-v fix-padding " >
                                <div class="warpbox ">
                                    <div class="">
                                        <i class="icon iconfont icon-incoming bigicon"></i>
                                        <span class="green money datatoday">0</span>
                                    </div>
                                    <div class="middle-font-size middle-font-color" >
                                        当前呼出总数（次）
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 padder-v fix-padding ">
                                <div class="warpbox remove-border">
                                    <div class="">
                                        <i class="icon iconfont icon-exhale bigicon"></i>
                                        <span class="green money datatoday">0</span>
                                    </div>
                                    <div class="middle-font-size middle-font-color" >
                                        当前呼入总数（次）
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </section>

            {{--<div class="row">--}}
                {{--<div class="col-md-4">--}}
                    {{--<div id="data-control" style="width: 500px; height: 400px; display: inline-block;">--}}

                    {{--</div>--}}
                {{--</div>--}}

                {{--<div class="col-md-8">--}}
                    {{--<section class="knob-section">--}}
                        {{--<div class="row remove-m-rl">--}}
                            {{--<div class="col-md-3 remove-p-l">--}}
                                {{--<div class="inline-block font right">--}}
                                    {{--<i class="iconfont icon-incoming knob-font call"></i>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                            {{--<div class="col-md-3 remove-p-l">--}}
                                {{--<div class="knob-box">--}}
                                    {{--<span class="number yellow">213</span>--}}
                                    {{--<span class="text block">累计呼出总数</span>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                            {{--<div class="col-md-3 col-md-offset-1 knob-child text-center">--}}
                                {{--<div class="knob-box">--}}

                                    {{--<span class="number yellow">13</span>--}}
                                    {{--<span class="text block">当前呼出总数</span>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}

                    {{--</section>--}}

                    {{--<section class="knob-section">--}}
                        {{--<div class="row remove-m-rl">--}}
                            {{--<div class="col-md-3 remove-p-l">--}}
                                {{--<div class="inline-block font right">--}}
                                    {{--<i class="iconfont icon-incoming knob-font out"></i>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                            {{--<div class="col-md-3 remove-p-l">--}}
                                {{--<div class="knob-box">--}}
                                    {{--<span class="number green">532</span>--}}
                                    {{--<span class="text block">累计呼入总数</span>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                            {{--<div class="col-md-3 col-md-offset-1 knob-child text-center">--}}
                                {{--<div class="knob-box">--}}

                                    {{--<span class="number green">10</span>--}}
                                    {{--<span class="text block">当前呼入总数</span>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</section>--}}

                {{--</div>--}}
            {{--</div>--}}


            <section class="">
                <div class="panel panel-common">
                    <div class="panel-heading">曲线统计</div>

                    <div class="panel-body">
                        <div id="ussd-chart" style=" height: 450px;"></div>
                    </div>

                </div>
            </section>

        </section>
    </section>

    <script src="{{ URL::asset('js/chartjs/echarts.min.js') }}"></script>
    <script src="{{ URL::asset('js/chartjs/wonderland.js') }}"></script>
    <script>
        window.onload = function () {
            control();
        }

        function control() {
            $('.spinner').show();
            setInterval(function () {
                ajaxsync("{{ url('admin/monitor/real') }}", {}, function (response) {
                   // var number = response.data.load

                    var data = [
                        Math.round(Math.random() * 100),
                        Math.round(Math.random() * 100),
                        Math.round(Math.random() * 100),
                        Math.round(Math.random() * 100)
                    ];
                    getStatistic(data)
                },'GET');
                $('.spinner').hide();
            },1000);
        }


        var x_data = [];
        var y_data = [[],[],[],[]];

        // 本周统计
        function getStatistic(data) {
            var val = getCurrentTime();
            x_data = getXdata(val);

            for(var i= 0; i< 4 ;i ++){
                y_data[i] = getYdata(y_data[i],data[i]);
            }
            initStatistic(y_data,x_data);
        }

        // 获取当前时间
        function getCurrentTime() {
            var date = new Date();
            var hour = date.getHours();
            var minute = date.getMinutes();
            var second = date.getSeconds();
            return hour + ':' +  minute + ":" + second;
        }

        // 获取x轴
        function getXdata(val) {
            if(x_data.length >24){
                x_data.splice(0,1);
            }
            x_data.push(val)
            return x_data;
        }

        // 获取y轴
        function getYdata(data,val) {

            if(data.length >24){
                data.splice(0,1);
            }
            data.push(val)
            return data;
        }

        //
        function initStatistic(data,x_data) {
            var app = echarts.init(document.getElementById('ussd-chart'),'wonderland');
            option = {
                animation: false,
                tooltip : {
                    trigger: 'axis',
                    axisPointer : {            // 坐标轴指示器，坐标轴触发有效
                        //type : 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
                    }
                },
                legend: {
                    // data:['累计呼出总数','累计呼入总数','当前呼出总数','当前呼入总数']
                    data:['当前呼出总数','当前呼入总数']
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                xAxis : [
                    {
                        type : 'category',
                        data :x_data
                    }
                ],
                yAxis : [
                    {
                        type : 'value',
                        max : 100,
                        min: 0
                    }
                ],
                series : [
//                    {
//                        name:'累计呼出总数',
//                        type:'line',
//                        data:data[0]
//                    },
//                    {
//                        name:'累计呼入总数',
//                        type:'line',
//                        data:data[1]
//                    },
                    {
                        name:'当前呼出总数',
                        type:'line',
                        data:data[2]
                    },
                    {
                        name:'当前呼入总数',
                        type:'line',
                        data:data[3]
                    },
                ]
            };
            app.setOption(option);
        }


    </script>
@endsection