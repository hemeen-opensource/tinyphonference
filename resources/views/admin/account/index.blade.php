@extends('admin.layouts.app')

@section('content')
    <div class="app-content">
        <ol class="breadcrumb">
            <li><i class="iconfont icon-index block"></i></li>
            <li><a href="{{ url('admin') }}">首页</a></li>
            <li class="active">账号管理</li>
        </ol>
        <section class="child-content">
            @if (session('result'))
                <p class="alert alert-success">
                    {{ session('result') }}
                </p>
            @endif
            @if (count($errors) > 0)
                @foreach ($errors->all() as $error)
                    <div class="alert alert-danger" role="alert">
                        {{ $error }}
                    </div>
                @endforeach
            @endif
            <div class="common-search">
                <form action="{{ url('admin/account') }}" method="get">
                    {{ csrf_field() }}
                    <div class="btn-group">
                        <input type="text" name="name" type="text" value="{{ isset($search['name']) ? $search['name'] : "" }}" class="form-control" placeholder="用户名"/>
                    </div>
                    <div class="btn-group">
                        <button type="submit" class="btn btn-primary">查询</button>
                    </div>
                    <div class="btn-group">
                        <a href="{{ url('admin/account/create')  }}" class="btn btn-primary">创建管理员账号</a>
                    </div>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th class="th-sortable" data-toggle="class">用户名</th>
                        <th>手机号码</th>
                        <th>邮箱</th>
                        <th>qq</th>
                        <th>最后登录时间</th>
                        <th>最后登录IP</th>
                        <th>角色</th>
                        <th>备注</th>
                        <th>创建时间</th>
                        <th class="text-center">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($accounts as $account)
                        <tr>
                            <td>{{ $account->name }}</td>
                            <td>{{ $account->mobile }}</td>
                            <td>{{ $account->email }}</td>
                            <td>{{ $account->qq }}</td>
                            <td>{{ $account->login_time }}</td>
                            <td>{{ $account->ip }}</td>
                            <td>{{ $account->administrator==1 ? '超级管理员' : '普通管理员' }}</td>
                            <td>{{ $account->remark }}</td>
                            <td>{{ $account->created_at }}</td>
                            <td class="text-center">
                                @if ($account->administrator == 0)
                                    <a class="delete-account cursor" data-href="{{ url('admin/account/delete',[$account->id]) }}" >删除</a>
                                    <a href="{{ url('admin/account/detail',[$account->id]) }}">修改</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center" colspan="6">暂无管理员数据</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="text-right">
                {!! $accounts->appends([
                     'name'=>isset($search['name']) ? $search['name'] : ""
                 ])->links() !!}
            </div>
        </section>
    </div>
    <script>
        window.onload = function () {
            $('.delete-account').click(function () {
                var cfm = confirm("确认删除此管理员账号");
                if(cfm)
                    window.location.href = $(this).attr('data-href')
            });
        }
    </script>
@endsection