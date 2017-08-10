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
                    <a href="{{ url('admin/log/bill') }}" class="btn btn-primary deepbluebg">账单</a>
                </div>
                <div class="btn-group">
                    <a href="{{ url('admin/log/call') }}" class="btn btn-primary ">呼叫详情记录</a>
                </div>
                <div class="btn-group">
                    <a href="{{ url('admin/log/conf') }}" class="btn btn-primary">会议详情记录</a>
                </div>
            </div>
            <div class="alert alert-success" role="alert">
                提供数据查询导出历史记录Excel
            </div>
            <form action="{{ url('admin/log/bill') }}" id="search-form" method="get" class="form-horizontal">
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
                        <a class="btn btn-primary margin-left" onclick="editUrl('{{ url('admin/log/bill') }}')" >查询</a>
                    </div>
                    <div class="btn-group margin-left">
                        <a onclick="editUrl('{{ url('admin/log/excel',['bill']) }}')" class="btn btn-primary">导出EXCEL</a>
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

                    @forelse($logs as $item)
                        <tr>
                            {{--<td>{{ $item->id }}</td>--}}
                            <td>{{ $item->callbegintime }}</td>
                            <td>{{ $item->connectbegintime }}</td>
                            <td>{{ $item->callendtime }}</td>
                            <td>{{ $item->callfailcause }}</td>
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