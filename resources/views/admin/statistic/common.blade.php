<div class="common-search">
    <div class="btn-group">
        <input type="radio" name="time"  value="day" v-model="origin.type" class="selectdata" checked/>时统计
    </div>
    <div class="btn-group">
        <input type="radio" name="time"  value="month" v-model="origin.type" class="selectdata"/>日统计
    </div>
    <div class="btn-group">
        <input type="radio" name="time" value="year" v-model="origin.type" class="selectdata" />月统计
    </div>
</div>


<div class="common-search">

    <div class="input-group">
        <!-- 1天内 -->
        <div id="dayDate" class="none inline-block">
            <div class="btn-group">
                <input class="form-day form-control" type="text" name="day" value="{{ date('Y-m-d H',strtotime(date('Y-m-d'))) }}" v-model="origin.start_day" data-date-format="yyyy-mm-dd hh" >
            </div>
            <div class="btn-group padding-10">
                <span style="padding: 0 10px;">至</span>
            </div>
            <div class="btn-group"  >
                <input class="form-day form-control" type="text" name="day" value="{{ date('Y-m-d H') }}" v-model="origin.end_day" data-date-format="yyyy-mm-dd hh" >
            </div>
        </div>
        <!-- 1月内 -->
        <div class="btn-group none" id="monthDate" >
            <div class="btn-group">
                <input class="form-month form-control" type="text" name="month" value="{{ date('Y-m-d',strtotime(date('Y-m'))) }}" v-model="origin.start_month" data-date-format="yyyy-mm-dd" >
            </div>
            <div class="btn-group padding-10">
                <span style="padding:10px;display:block;">至</span>
            </div>
            <div class="btn-group"  >
                <input class="form-month form-control" type="text" name="month" value="{{ date('Y-m-d') }}" v-model="origin.end_month" data-date-format="yyyy-mm-dd" >
            </div>
        </div>
        <!-- 1年内 -->
        <div class="btn-group none" id="yearDate" >
            <div class="btn-group">
                <input class="form-year form-control" type="text" name="year" value="{{ date('Y-m',strtotime(date('Y-01'))) }}" v-model="origin.start_year" data-date-format="yyyy-mm" >
            </div>
            <div class="btn-group padding-10">
                <span style="padding:10px;display: inline-block">至</span>
            </div>
            <div class="btn-group"  >
                <input class="form-year form-control" type="text" name="year" value="{{ date('Y-m') }}" v-model="origin.end_year" data-date-format="yyyy-mm" >
            </div>
        </div>
        <div class="btn-group margin-left-10" style="margin-left: 10px;">
            <a class="btn btn-primary">查询</a>
        </div>
    </div>
</div>




<script>
    var x_data  = [];
    window.onload = function () {
        var appSize = '10';
        $(window).resize(function () {
            if(appSize>0) {
                initStatistic(y_data,x_data);
            }
        });

        $('.form-day').datetimepicker({
            language:  'zh-CN',
            format:'yyyy-mm-dd hh',
            weekStart: 1,
            todayBtn:  1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 2,
            minView:1,
            maxView:2,
            forceParse: 0,
            showMeridian: 1
        });


        $('.form-month').datetimepicker({
            language:  'zh-CN',
            format:'yyyy-mm-dd',
            weekStart: 1,
            todayBtn:  1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 3,
            minView:2,
            maxView:3,
            forceParse: 0,
            showMeridian: 1
        });

        $('.form-year').datetimepicker({
            format:'yyyy-mm',
            language:  'zh-CN',
            todayBtn:  1,
            startView: 4,
            minView:3,
            maxView:4,
            autoclose: 1,
            forceParse: 0
        });

        var vue = new Vue({
                el: '#vue-statistic',
                data: {
                    origin: {
                        type:'day',
                        crf_token: "{{ csrf_token() }}"
                    },
                    x_data: []
                },
                watch: {
                    origin:{
                        handler:function (val,oldVal) {
                            this.changeStatistic()
                        },
                        deep: true
                    }
                },
                methods: {
                    changeStatistic: function () {
                        meetDate(this.origin);
                        changeDate(this.origin.type);

                    }
                }
            }
        );
    }

    function changeDate(type) {
        if(type=='day'){
            $('#dayDate').removeClass('none');
            $('#monthDate').addClass('none');
            $('#yearDate').addClass('none');
        }
        if(type=='month'){
            $('#dayDate').addClass('none');
            $('#monthDate').removeClass('none');
            $('#yearDate').addClass('none');
        }
        if(type=='year'){
            $('#dayDate').addClass('none');
            $('#monthDate').addClass('none');
            $('#yearDate').removeClass('none');
        }
    }


    function meetDate(origin) {
        var create_data = '';
        switch (origin.type){
            case 'day':
                create_data = meetDay(origin.start_day,origin.end_day);
                break;
            case 'month':
                create_data = meetMonth(origin.start_month,origin.end_month);
                break;
            case 'year':
                create_data = meetYear(origin.start_year,origin.end_year);
                break;
        }
        if(isString(create_data)){
            showtoast(create_data);
            return;
        }
        x_data = create_data;
        // 执行自定义方法
        getStatistic(origin);

    }

    // 检验是否同一天
    function meetDay(start_time,end_time) {
        var arr1 = start_time.substring(0,10).split("-");
        var hour1 = start_time.substring(11,13);
        var starttime = new Date(arr1[0], arr1[1], arr1[2],hour1);
        var starttimes = starttime.getTime();
        var arr2 = end_time.substring(0,10).split("-");
        var hour2 = end_time.substring(11,13);
        var endtime = new Date(arr2[0], arr2[1], arr2[2],hour2);
        var endtimes = endtime.getTime();
        if (starttimes > endtimes) {
            return '结束时间必须在开始时间后';
        }
        var startday = new Date(arr1[0], arr1[1], arr1[2]);
        var endday = new Date(arr2[0], arr2[1], arr2[2]);
        if(startday.getTime() != endday.getTime()){
            return '时间段必须在同一天内';
        }
        var time_data = [];
        var time = hour2 - hour1;
        if(time == 0){
            time_data.push(parseInt(hour1) + "时")
        } else{
            for(var i = hour1; i<= hour2; i++  ){
                time_data.push(parseInt(i) + "时")
            }
        }
        return time_data;
    }

    // 月校验
    function meetMonth(start_month,end_month) {
        var arr1 = start_month.split("-");
        var starttime = new Date(arr1[0], arr1[1], arr1[2]);
        var starttimes = starttime.getTime();
        var arr2 = end_month.split("-");
        var endtime = new Date(arr2[0], arr2[1], arr2[2]);
        var endtimes = endtime.getTime();
        if (starttimes > endtimes) {
            return '结束时间必须在开始时间后';
        }
        var startday = new Date(arr1[0], arr1[1]);
        var endday = new Date(arr2[0], arr2[1]);
        if(startday.getTime() != endday.getTime()){
            return '时间段必须在同一个自然月内';
        }
        var day_data = [];
        var day = arr2[2] - arr1[2];
        if(day == 0){
            day_data.push(parseInt(arr1[2]) + "日")
        } else{
            for(var i = arr1[2]; i<= arr2[2]; i++  ){
                day_data.push(parseInt(i)  + "日")
            }
        }
        return day_data;
    }

    // 年校验
    function meetYear(start_year,end_year) {
        var arr1 = start_year.split("-");
        var starttime = new Date(arr1[0], arr1[1]);
        var starttimes = starttime.getTime();
        var arr2 = end_year.split("-");
        var endtime = new Date(arr2[0], arr2[1]);
        var endtimes = endtime.getTime();
        if (starttimes > endtimes) {
            return '结束时间必须在开始时间后';
        }
        var startday = new Date(arr1[0]);
        var endday = new Date(arr2[0]);
        if(startday.getTime() != endday.getTime()){
            return '时间段必须在同一个自然年内';
        }
        var month_data = [];
        var month = arr2[1] - arr1[1];
        if(month == 0){
            month_data.push(parseInt(arr1[1]) + "月")
        } else{
            for(var i = arr1[1]; i<= arr2[1]; i++  ){
                month_data.push(parseInt(i) + "月")
            }
        }
        return month_data;
    }


    function isString(str){
        return (typeof str=='string')&&str.constructor==String;
    }

</script>