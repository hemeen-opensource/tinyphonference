<section class="app-header">
    <div class="account right">
        <span class="welcome">欢迎来到电话会议系统管理后台</span>
        <img src="{{ URL::asset('images/user.png') }}" class="than" width="40" height="40" />
        <span class="green">{{ Session::get('users.name') }}</span>
        <a class="than cursor" href="{{ url('admin/account/personal') }}">基本资料</a>
        <a href="{{ url('/admin/logout') }}" class="exit-btn greenbg while inline-block text-center than cursor">退出</a>
    </div>
</section>