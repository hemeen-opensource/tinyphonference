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
                    <a href="{{ url('admin/statistic') }}" class="btn btn-primary">呼叫数量统计</a>
                </div>
                <div class="btn-group">
                    <a href="{{ url('admin/statistic/call') }}" class="btn btn-primary deepbluebg">通话时长统计</a>
                </div>
                <div class="btn-group">
                    <a href="{{ url('admin/statistic/reason') }}" class="btn btn-primary">呼出分析统计</a>
                </div>
            </div>
            <!--时间工具-->
            @include('admin.statistic.common')


            <section class="statics">
                <div class="panel panel-common">
                    <div class="panel-heading">通话时长统计</div>
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
            call_in_time: [],
            call_out_time:[],
            call_in_num: [],
            call_out_num:[],
            count:[]
        };

        // 统计开始
        function getStatistic(origin) {
            $('.spinner').show();
            var params = {
                type: 'call_percent',
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
                console.log("x_data")
                console.log(x_data);

                initStatistic(y_data,x_data);
            },'GET');
        }

        function initStatistic(data,x_data) {
            var app = echarts.init(document.getElementById('ussd-chart'),'wonderland');
            option = {
                title: {
                    text: ''
                },
                tooltip: {
                    trigger: 'axis'
                },
                grid: {
                    left: '1%',
                    right: '3%',
                    bottom: '3%',
                    containLabel: true
                },
                toolbox: {
                    feature: {
                    }
                },
                xAxis: {
                    type: 'category',
                    boundaryGap: false,
                    data : x_data,
                    name: '',
                },
                yAxis: {
                    type: 'value',
                    name:'呼叫次数（次）',
                    nameGap:32,
                    axisLabel:{
                        margin:12
                    }
                },
                series: [
                    {
                        name:'呼出时长',
                        type:'line',
                        stack: '呼出',
                        areaStyle: {normal: {}},
                        data:data.call_out_time
                    },
                    {
                        name:'呼入时长',
                        type:'line',
                        stack: '呼出',
                        //areaStyle: {normal: {}},
                        data:data.call_in_time
                    },
                ]
            };

            app.setOption(option);
        }

    </script>
@endsection