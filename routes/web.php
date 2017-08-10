<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// 后台
Route::any('/', 'Admin\IndexController@login');
// 验证码
Route::get('captcha-img', function(){return captcha_img('flat');}); // 图形验证码
Route::get('/encrypt/{password}', 'Admin\IndexController@encrypt'); // 获取加密串

Route::get('/create', 'Admin\LogController@create'); // 生成假数据


/**
 * 总后台管理
 */
Route::group(['namespace' => 'Admin', 'prefix' => 'admin','middleware'=>'admin'], function() {

    /**
     * 首页
     */
    Route::get('/', 'IndexController@index');
    Route::get('/logout ', 'IndexController@logout');

    /**
     * 账号管理
     */
//    Route::match(['get', 'post'], '/', function () {
//        //
//    });

    Route::get('/account', 'AccountController@index');
    Route::match(['get','post'],'/account/create', 'AccountController@create');
    Route::any('/account/detail/{id}', 'AccountController@detail');
    Route::get('/account/delete/{id}', 'AccountController@delete');
    Route::any('/account/personal', 'AccountController@personal');

    /**
     * 监控管理
     */
    Route::get('/monitor', 'MonitorController@index');
    Route::get('/monitor/real', 'MonitorController@real');

    /**
     * 日志管理
     */
    Route::get('/log/bill', 'LogController@bill');
    Route::get('/log/call', 'LogController@call');
    Route::get('/log/conf', 'LogController@conf');
    Route::get('/log/create', 'LogController@create');
    Route::match(['post','get'],'/log/excel/{type}', 'LogController@excel');

    /**
     * 统计查询
     */
    Route::get('/statistic', 'StatisticsController@index');
    Route::get('/statistic/call', 'StatisticsController@call');
    Route::get('/statistic/reason', 'StatisticsController@reason');
    Route::get('/statistic/find', 'StatisticsController@getStatistic');
    Route::get('/statistic/start', 'StatisticsController@start');

});