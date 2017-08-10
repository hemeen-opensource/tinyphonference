@extends('admin.layouts.app')

@section('content')
    <section class="app-content">
        <ol class="breadcrumb">
            <li> <i class="iconfont icon-index block"></i></li>
            <li><a href="{{ url('admin') }}">首页</a></li>
        </ol>
        <section class="child-content">


            <section class="">
                <div class="panel panel-common">

                    <div class="panel-body"  style="position: relative">

                        <div class="row">
                            <div class="col-md-12 text-center" style="background-color: rgba(204, 237, 247, 0.1);">
                                <div class="spinner"></div>
                                <div id="data-control" style="height: 450px;"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div id="ussd-chart" style=" height: 400px;"></div>
                            </div>
                        </div>

                    </div>

                </div>
            </section>

        </section>
    </section>

    <script src="{{ URL::asset('js/chartjs/echarts.min.js') }}"></script>
    <script src="{{ URL::asset('js/echartstheme/wonderland.js') }}"></script>
    <script>

        var x_data = [];
        var y_data = [];

        window.onload = function () {
            $('.spinner').show();
            // getStatistic();
            control();
        }

        // 实时件欧空
        function control() {
            var appControl = echarts.init(document.getElementById('data-control'),'wonderland');
            options = {
                tooltip : {
                    formatter: "{a} <br/>{b} : {c}%"
                },
                series: [
                    {
                        name: '系统负载',
                        type: 'gauge',
                        detail: {formatter:'{value}%'},
                        data: [{value: 50, name: '系统负载'}]
                    }
                ]
            };
            setInterval(function () {
                ajaxsync("{{ url('admin/monitor/real') }}", {}, function (response) {
                    var number = response.data.load
                    // var number = (Math.random() * 100).toFixed(0) - 0;
                    options.series[0].data[0].value = number;
                    getStatistic(number)
                },'GET');
                $('.spinner').hide();

                appControl.setOption(options, true);
            },1000);

        }


        // 获取当前时间
        function getCurrentTime() {
            var date = new Date();
            var hour = date.getHours();
            var minute = date.getMinutes();
            var second = date.getSeconds();
            return hour + ':' +  minute + ":" + second;
        }

        function getXdata(val) {
            if(x_data.length >24){
                x_data.splice(0,1);
            }
            x_data.push(val)
            return x_data;
        }

        function getYdata(val) {
            if(y_data.length >24){
                y_data.splice(0,1);
            }
            y_data.push(val)
            return y_data;
        }

        // 本周统计
        function getStatistic(number) {
            var val = getCurrentTime();
            x_data = getXdata(val);
            y_data = getYdata(number);
            initStatistic(y_data,x_data);
        }

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
                    data:['实时监控']
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
                    {
                        name:'实时监控',
                        type:'line',
                        stack: '呼入',
                        barWidth : 30,
                        data:data
                    },
                ]
            };
            app.setOption(option);
        }

    </script>
@endsection