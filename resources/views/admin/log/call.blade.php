@extends('admin.layouts.app')

@section('content')
    <div class="app-content">
        <ol class="breadcrumb">
            <li><i class="iconfont icon-index block"></i></li>
            <li><a href="{{ url('admin') }}">首页</a></li>
            <li class="active">详单记录</li>
        </ol>
        <section class="child-content">

            <div class="common-search">
                <div class="btn-group">
                    <a href="{{ url('admin/log/bill') }}" class="btn btn-primary">账单</a>
                </div>
                <div class="btn-group">
                    <a href="{{ url('admin/log/call') }}" class="btn btn-primary deepbluebg">呼叫详情记录</a>
                </div>
                <div class="btn-group">
                    <a href="{{ url('admin/log/conf') }}" class="btn btn-primary">会议详情记录</a>
                </div>
            </div>
            <div class="alert alert-success" role="alert">
                提供数据查询导出历史记录Excel
            </div>
            <form action="{{ url('admin/log/call') }}" id="search-form" method="get" class="form-horizontal">
                {{ csrf_field() }}
                <div class="common-search">
                    <span class="btn-group field-right">时间：</span>
                    <div class="btn-group">
                        <input type="text" name="start_time" class="form-control form-month" placeholder=""
                               value="{{ isset($search['start_time']) ? $search['start_time'] : date('Y-m-01')}}"/>
                    </div>
                    <span class="btn-group field">至</span>
                    <div class="btn-group">
                        <input type="text" name="end_time" class="form-control form-month" placeholder=""
                               value="{{ isset($search['end_time']) ? $search['end_time'] : date('Y-m-d',(strtotime(date('Y-m-01')." +1 month")) -1) }}"/>
                    </div>
                    <div class="btn-group">
                        <span class="search-title"> 用户号码：</span>
                    </div>
                    <div class="btn-group">
                        <input type="text" name="mobile" type="text"  value="{{ isset($search['mobile']) ? $search['mobile'] : "" }}" class="form-control" placeholder="用户的号码"/>
                    </div>
                </div>
                <div class="common-search">
                    <div class="btn-group">
                        运营商：
                    </div>
                    <div class="btn-group">
                        <select name="operator" class="form-control">
                            <option value="-1" {{ isset($search['operator']) ? $search['operator'] == -1 ? 'selected' : 'false' : 'false'}}>全部</option>
                            <option value="0" {{ isset($search['operator']) ? $search['operator'] == 0 ? 'selected' : 'false' : 'false'}}>电信手机</option>
                            <option value="1" {{ isset($search['operator']) ? $search['operator'] == 1 ? 'selected' : 'false' : 'false'}}>移动手机</option>
                            <option value="2" {{ isset($search['operator']) ? $search['operator'] == 2 ? 'selected' : 'false' : 'false'}}>联通手机</option>
                            <option value="3" {{ isset($search['operator']) ? $search['operator'] == 3 ? 'selected' : 'false' : 'false'}}>固定电话</option>
                        </select>
                    </div>
                    <div class="btn-group">
                        <span class="search-title"> 呼叫方向：</span>
                    </div>
                    <div class="btn-group">
                        <select name="dir" class="form-control">
                            <option value="-1" {{ isset($search['dir']) ? $search['dir'] == -1 ? 'selected' : 'false' : 'false'}}>全部</option>
                            <option value="0" {{ isset($search['dir']) ? $search['dir'] == 1 ? 'selected' : 'false' : 'false'}}>呼入</option>
                            <option value="1" {{ isset($search['dir']) ? $search['dir'] == 2 ? 'selected' : 'false' : 'false'}}>呼出</option>
                        </select>
                    </div>
                    <div class="btn-group">
                        <span class="search-title"> 是否呼通：</span>
                    </div>
                    <div class="btn-group">
                        <select name="callstatus" class="form-control">
                            <option value="-1" {{ isset($search['callstatus']) ? $search['callstatus'] == -1 ? 'selected' : 'false' : 'false'}}>全部</option>
                            <option value="1" {{ isset($search['callstatus']) ? $search['callstatus'] == 1 ? 'selected' : 'false' : 'false'}}>是</option>
                            <option value="0" {{ isset($search['callstatus']) ? $search['callstatus'] == 0 ? 'selected' : 'false' : 'false'}}>否</option>
                        </select>
                    </div>
                    <div class="btn-group">
                        <span class="search-title"> 通话时长：</span>
                    </div>
                    <div class="btn-group">
                        <select name="talkduration" class="form-control">
                            <option value="-1" {{ isset($search['talkduration']) ? $search['talkduration'] == -1 ? 'selected' : 'false' : 'false'}}>不限</option>
                            <option value="1" {{ isset($search['talkduration']) ? $search['talkduration'] == 1 ? 'selected' : 'false' : 'false'}}>1分钟内</option>
                            <option value="30" {{ isset($search['talkduration']) ? $search['talkduration'] == 30 ? 'selected' : 'false' : 'false'}}>1-30分钟内</option>
                            <option value="60" {{ isset($search['talkduration']) ? $search['talkduration'] == 60 ? 'selected' : 'false' : 'false'}}>30-60分钟内</option>
                            <option value="180" {{ isset($search['talkduration']) ? $search['talkduration'] == 180 ? 'selected' : 'false' : 'false'}}>60分钟以上</option>
                        </select>
                    </div>

                    <div class="btn-group">
                        <a class="btn btn-primary margin-left" onclick="editUrl('{{ url('admin/log/call') }}')" >查询</a>
                    </div>
                    <div class="btn-group margin-left">
                        <a onclick="editUrl('{{ url('admin/log/excel',['call']) }}')" class="btn btn-primary">导出EXCEL</a>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        {{--<th class="th-sortable" data-toggle="class">ID</th>--}}
                        <th>开始时间</th>
                        <th>接通时间</th>
                        <th>结束时间</th>
                        <th>结束原因</th>
                        <th>是否接通</th>
                        <th>挂断类型</th>
                        <th>呼叫方向</th>
                        <th>主叫</th>
                        <th>被叫</th>
                        <th>号码所属运营商</th>
                        <th>通话时间（秒）</th>
                        <th>通话分钟数</th>
                    </tr>
                    </thead>
                    <tbody>
                    {{--//        `id` VARCHAR(64) NOT NULL COMMENT 'UUID',--}}
                    {{--//  `nodeid` VARCHAR(256) NOT NULL COMMENT 'IPSC节点ID（格式：区域ID.站ID.IPSC实例ID）',--}}
                    {{--//  `cdrid` VARCHAR(256) NOT NULL COMMENT 'CDR 记录ID',--}}
                    {{--//  `processid` VARCHAR(256) NOT NULL COMMENT '流水号（全局唯一，IPSC实例启动时开始计算，单个实例期间严格递增）',--}}
                    {{--//  `callid` VARCHAR(256) NOT NULL COMMENT '呼叫标识号（节点内全局唯一）',--}}
                    {{--//  `ch` INT NOT NULL COMMENT '通道号：因交换机初始化时间不同，通道号可能会变化',--}}
                    {{--//  `cdrcol` VARCHAR(256) NULL,--}}
                    {{--//  `devno` VARCHAR(256) NOT NULL COMMENT '设备号： \n中继：格式 “0:0:1:1”—“交换机号:板号:中继号:通道号”；\nSIP：格式“0:0:1”—“交换机号:板号:通道号”；\nFXO：格式“0:0:1”—“交换机号:板号:通道号”；',--}}
                    {{--//  `ani` VARCHAR(256) NULL COMMENT '主叫号码',--}}
                    {{--//  `dnis` VARCHAR(256) NULL COMMENT '被叫号码',--}}
                    {{--//  `dnis2` VARCHAR(256) NULL COMMENT '原被叫号码',--}}
                    {{--//  `orgcallno` VARCHAR(256) NULL COMMENT '原始号码',--}}
                    {{--//  `dir` INT NOT NULL COMMENT '呼叫方向 \n0: 呼入\n1: 呼出\n2: 内部呼叫（保留）',--}}
                    {{--//  `devtype` INT NOT NULL COMMENT '通道设备类型 \n1: 中继\n2: SIP\n3: H323\n4: 模拟外线\n5: 模拟内线\n10: 逻辑通道',--}}
                    {{--//  `busitype` INT NULL,--}}
                    {{--//  `callstatus` INT NOT NULL COMMENT '呼通标志 \n0: 呼叫未接通\n1: 呼叫接通',--}}
                    {{--//  `endtype` INT NOT NULL COMMENT '结束类型 \n0: 空（初始值，未定义）\n1: 本地拆线\n2: 远端拆线\n3: 设备拆线',--}}
                    {{--//  `ipscreason` INT NULL COMMENT '呼叫失败原因：IPSC定义reason值',--}}
                    {{--//  `callfailcause` INT NULL COMMENT '呼叫失败原因：设备、SS7、PRI、SIP的失败cause值',--}}

                    @forelse($logs as $item)
                        <tr>
                            {{--<td>{{ $item->id }}</td>--}}
                            <td>{{ $item->callbegintime }}</td>
                            <td>{{ $item->connectbegintime }}</td>
                            <td>{{ $item->callendtime }}</td>
                            <td>{{ $item->callfailcause }}</td>
                            <td>{{ $item->callstatus }}</td>
                            <td>{{ $item->endtype }}</td>
                            <td>
                               {{$item->dir}}
                            </td>
                            <td>{{ $item->ani }}</td>
                            <td>{{ $item->dnis }}</td>
                            <td>中国电信</td>
                            <td>{{ $item->talkduration }}</td>
                            <td>{{ $item->minute }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center" colspan="12">暂无数据</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="text-right">
                {!! $logs->appends([
                   'mobile'=>isset($search['mobile']) ? $search['mobile'] : "",
                   'start_time'=>isset($search['start_time']) ? $search['start_time'] : "",
                   'end_time'=>isset($search['end_time']) ? $search['end_time'] : "",
                   'dir'=>isset($search['dir']) ? $search['dir'] : "",
                   'callstatus'=>isset($search['callstatus']) ? $search['callstatus'] : "",
                   'talkduration'=>isset($search['talkduration']) ? $search['talkduration'] : "",
               ])->links() !!}
            </div>
        </section>
    </div>
    <script>
        window.onload = function () {

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
        }
        
        function editUrl(url) {

            var start_time = $('input[name=start_time]').val()
            var end_time = $('input[name=end_time]').val()
            if(!start_time){
                showtoast("请选择开始时间")
                return
            }
            if(!end_time){
                showtoast("请选择结束时间")
                return
            }

            var title = coomonMeetMonth(start_time,end_time)
            if(title){
                showtoast(title)
                return
            }
            document.getElementById('search-form').action = url;
            document.getElementById("search-form").submit();
        }
    </script>
@endsection