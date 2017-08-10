<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('password');
            $table->tinyInteger('administrator')->comment('超级管理员 1 为超级管理员')->default(0);
            $table->string('email')->comment('邮箱')->nullable();
            $table->string('mobile')->comment('手机号码')->nullable();
            $table->string('qq')->comment('qq')->nullable();
            $table->string('ip')->comment('本次登录ip')->nullable();
            $table->timestamp('login_time')->comment('本次登录时间')->nullable();
            $table->timestamp('last_login_time')->comment('上次登录时间')->nullable();
            $table->string('last_ip')->comment('上次登录ip')->nullable();
            $table->text('remark')->comment('备注')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
