<div class="app-sidebar">
    <ul>
        <li class="logo-box position-layout">
            <a href="{{ url('admin') }}">电话会议系统</a>
        </li>
        @if(Session::get('administrator'))
            <li class="nav pink-left-border {{isset($parent_active['parent_account']) ? $parent_active['parent_account'] : ''}}">
                <a  href="{{ url('admin/account') }}">
                    <i class="iconfont icon-account block"></i>
                    <span class="block">账号管理</span>
                </a>
            </li>
        @endif
        <li class="nav blue-left-border {{isset($parent_active['parent_log']) ? $parent_active['parent_log'] : ''}}">
            <a  href="{{ url('admin/log/bill') }}">
                <i class="iconfont icon-template block"></i>
                <span class="block">详单记录</span>
            </a>
        </li>
        <li class="nav lightgreen-left-border {{isset($parent_active['parent_monitor']) ? $parent_active['parent_monitor'] : ''}}">
            <a  href="{{ url('admin/monitor') }}">
                <i class="iconfont icon-monitor block"></i>
                <span class="block">监控数据</span>
            </a>
        </li>
        <li class="nav yellow-left-border {{isset($parent_active['parent_statistic']) ? $parent_active['parent_statistic'] : ''}}">
            <a  href="{{ url('admin/statistic') }}">
                <i class="iconfont icon-statistics block"></i>
                <span class="block">统计分析</span>
            </a>
        </li>
    </ul>

</div>