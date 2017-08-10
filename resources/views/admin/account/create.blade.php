@extends('admin.layouts.app')

@section('content')
    <div class="app-content">
        <ol class="breadcrumb">
            <li> <i class="iconfont icon-index block"></i></li>
            <li><a href="{{ url('admin') }}">首页</a></li>
            <li><a href="{{ url('admin/account') }}">账号管理</a></li>
            <li class="active">创建管理员账号</li>
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
            <div class="portal-form">
                <form class="form-horizontal" method="post" action="{{ url('admin/account/create') }}" id="createForm">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label class="control-label extend-label inline-block left">用户名 :</label>
                        <input type="text" name="name" class="form-control extend-large-input inline-block left" placeholder="请输入用户名">
                        <span class="inline-block tip-require">*</span>
                    </div>
                    <div class="form-group">
                        <label class="control-label extend-label inline-block left">密码 :</label>
                        <input type="password" name="password" class="form-control extend-large-input left" placeholder="请输入密码">
                        <span class="inline-block tip-require">*</span>
                    </div>
                    <div class="form-group">
                        <label class="control-label extend-label inline-block left">重复密码 :</label>
                        <input type="password" name="repassword" class="form-control extend-large-input left" placeholder="请输入重复密码">
                        <span class="inline-block tip-require">*</span>
                    </div>
                    <div class="form-group">
                        <label class="control-label extend-label inline-block left">手机号码 :</label>
                        <input type="text" name="mobile" class="form-control extend-large-input left" placeholder="请输入手机号码">
                    </div>
                    <div class="form-group">
                        <label class="control-label extend-label inline-block left"> 邮箱:</label>
                        <input type="text" name="email" class="form-control extend-large-input left" placeholder="请输入邮箱">
                    </div>
                    <div class="form-group">
                        <label class="control-label extend-label inline-block left"> QQ:</label>
                        <input type="text" name="qq" class="form-control extend-large-input left" placeholder="请输入QQ">
                    </div>
                    <div class="form-group">
                        <label class="control-label extend-label inline-block left">备注 :</label>
                        <textarea name="remark"  cols="30" rows="10" class="form-control extend-large-input left"></textarea>
                    </div>
                    <div class="form-group">
                        <label class="control-label extend-label inline-block left"></label>
                        <button id="validateBtn" type="button" class="btn btn-primary">保存</button>
                    </div>
                </form>
            </div>
        </section>
    </div>
    <script>
        window.onload = function () {
            $('#validateBtn').click(function () {
                $('#createForm').bootstrapValidator('validate');
                var res = $('#createForm').data('bootstrapValidator').isValid();
                if (res){
                    document.getElementById('createForm').submit();
                }
            });
            $('#createForm').bootstrapValidator({
                fields: {
                    name: {
                        validators: {
                            notEmpty: {
                                message: '请输入用户名'
                            }
                        }
                    },
                    password: {
                        validators: {
                            notEmpty: {
                                message: '请输入密码'
                            },
                            stringLength: {
                                min: 6,
                                max: 12,
                                message: '请输入6到12位密码'
                            }
                        }
                    },
                    repassword : {
                        validators: {
                            notEmpty: {
                                message: '请输入密码'
                            },
                            stringLength: {
                                min: 6,
                                max: 12,
                                message: '请输入6到12位密码'
                            }
                        }
                    }
                }
            });

        }
    </script>


@endsection