@extends('admin.layouts.app')

@section('content')
    <div class="app-content" id="vue-statistic">
        <ol class="breadcrumb">
            <li> <i class="iconfont icon-index block"></i></li>
            <li><a href="{{ url('portal') }}">首页</a></li>
            <li class="active">统计分析</li>
        </ol>
        <section class="child-content">
            <div class="common-search">
                <div class="btn-group">
                    <a href="{{ url('admin/statistic') }}" class="btn btn-primary deepbluebg">呼叫数量统计</a>
                </div>
                <div class="btn-group">
                    <a href="{{ url('admin/statistic/call') }}" class="btn btn-primary">通话时长统计</a>
                </div>
                <div class="btn-group">
                    <a href="{{ url('admin/statistic/reason') }}" class="btn btn-primary">呼出分析统计</a>
                </div>
            </div>
            <!--时间工具-->
            @include('admin.statistic.common')


            <section class="statics">
                <div class="panel panel-common">
                    <div class="panel-heading">呼叫数量统计</div>
                    <div class="panel-body" style="position: relative">
                        <div class="spinner"></div>
                        <div id="ussd-chart" style=" height: 450px;"></div>
                    </div>
                </div>
            </section>

        </section>
    </div>


    <script src="{{ URL::asset('js/chartjs/echarts.min.js') }}"></script>
    <script src="{{ URL::asset('js/echartstheme/wonderland.js') }}"></script>

    <script>

        var y_data = {
            call_all: [],
            call_in:[],
            call_out_success: [],
            call_out_fail:[],
            call_effect:[]
        };

        // 统计开始
        function getStatistic(origin) {
            $('.spinner').show();
            var params = {
                type: 'call',
                date_type: origin.type,
                crf_token: origin.crf_token,
            };
            if(params.date_type == 'day'){
                params.start_time = origin.start_day + ':00:00';
                params.end_time = origin.end_day +":59:59";
            }else if(params.date_type =='month'){
                params.start_time = origin.start_month;
                params.end_time = origin.end_month;
            }else{
                params.start_time = origin.start_year;
                params.end_time = origin.end_year;
            }
            ajaxsync("{{ url('admin/statistic/start') }}", params, function (response) {
                $('.spinner').hide();
                var data = response.data
                y_data = data;
                initStatistic(y_data,x_data);
            },'GET');
        }

        function initStatistic(data,x_data) {
            var app = echarts.init(document.getElementById('ussd-chart'),'wonderland');
            option = {
                tooltip : {
                    trigger: 'axis',
                    axisPointer : {            // 坐标轴指示器，坐标轴触发有效
                        type : 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
                    }
                },
                legend: {
                    data:['呼入数量','呼出成功数量','呼出失败数量','有效呼叫总数']
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
                        data: x_data
                    }
                ],
                yAxis : [
                    {
                        type : 'value'
                    }
                ],
                series : [

                    {
                        name:'呼入数量',
                        type:'bar',
                        stack: '呼入',

                        data:data.call_in
                    },
                    {
                        name:'呼出成功数量',
                        type:'bar',
                        stack: '呼出',

                        data:data.call_out_success
                    },
                    {
                        name:'呼出失败数量',
                        type:'bar',
                        stack: '呼出',

                        data:data.call_out_fail
                    },
                    {
                        name:'有效呼叫总数',
                        type:'line',
                        stack: '有效数',

                        data:data.call_effect
                    }
                ]
            };
            app.setOption(option);
        }

    </script>
@endsection